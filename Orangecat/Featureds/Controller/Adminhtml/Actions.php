<?php
/**
 * Orange Cat
 * Copyright (C) 2018 Orange Cat
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see http://opensource.org/licenses/gpl-3.0.html
 *
 * @category Orangecat
 * @package Orangecat_Featureds
 * @copyright Copyright (c) 2018 Orange Cat
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Oliverio Gombert <olivertar@gmail.com>
 */

namespace Orangecat\Featureds\Controller\Adminhtml;

abstract class Actions extends \Magento\Backend\App\Action
{
    protected $_formSessionKey;

    protected $_allowedKey;

    protected $_modelClass;

    protected $_activeMenu;

    protected $_configSection;

    protected $_idKey = 'id';

    protected $_statusField     = 'status';

    protected $_paramsHolder;

    protected $_model;

    protected $_coreRegistry = null;

    public function execute()
    {
        $_preparedActions = array('index', 'grid', 'new', 'edit', 'save', 'delete', 'config', 'massStatus', 'relatedproducts', 'relatedproductsgrid');
        $_action = $this->getRequest()->getActionName();
        if (in_array($_action, $_preparedActions)) {
            $method = '_'.$_action.'Action';

            $this->_beforeAction();
            $this->$method();
            $this->_afterAction();
        }
    }

    protected function _indexAction()
    {
        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }
        
        $this->_view->loadLayout();
        $this->_setActiveMenu($this->_activeMenu);
        $title = __('Manage '.$this->_getModel(false)->getOwnTitle(true));
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_addBreadcrumb($title, $title);
        $this->_view->renderLayout();
    }

    protected function _gridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    protected function _newAction()
    {
        $this->_forward('edit');
    }

    public function _editAction()
    {
        $model = $this->_getModel();

        $this->_getRegistry()->register('current_model', $model);

        $this->_view->loadLayout();
        $this->_setActiveMenu($this->_activeMenu);

        $title = $model->getOwnTitle();

        if ($model->getId()) {
            $breadcrumbTitle = __('Edit '.$title);
            $breadcrumbLabel = $breadcrumbTitle;
        } else {
            $breadcrumbTitle = __('New '.$title);
            $breadcrumbLabel = __('Create '.$title);
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__($title));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $this->_getModelName($model) : __('New '.$title)
        );

        $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);

        $values = $this->_getSession()->getData($this->_formSessionKey, true);
        if ($this->_paramsHolder) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(print_r($this->_paramsHolder, true));
            $values = isset($values[$this->_paramsHolder]) ? $values[$this->_paramsHolder] : null;
        }

        if ($values) {
            $model->addData($values);
        }

        $this->_view->renderLayout();
    }

    protected function _getModelName(\Magento\Framework\Model\AbstractModel $model)
    {
        return $model->getName() ?: $model->getTitle();
    }

    public function _saveAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/*'));
        }
        $model = $this->_getModel();
        try {
            $featuredsform = $request->getParam('featured');
            $model->addData($featuredsform);

            $this->_beforeSave($model, $request);
            $model->save();
            $this->_afterSave($model, $request);

            $this->messageManager->addSuccess(__($model->getOwnTitle().' has been saved.'));
            $this->_setFormData(false);

            if ($request->getParam('back')) {
                $this->_redirect('*/*/edit', [$this->_idKey => $model->getId()]);
            } else {
                $this->_redirect('*/*');
            }
            return;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError(nl2br($e->getMessage()));
            $this->_setFormData();
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving '.strtolower($model->getOwnTitle()).'.').' '.$e->getMessage());
            $this->_setFormData();
        }

        $this->_redirect('*/*/edit', [$this->_idKey => $model->getId()]);
    }

    protected function _beforeSave($model, $request) {}

    protected function _afterSave($model, $request) {}

    protected function _beforeAction() {}

    protected function _afterAction() {}

    protected function _deleteAction()
    {
        $ids = $this->getRequest()->getParam($this->_idKey);

        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $error = false;
        try {
            foreach($ids as $id) {
                $this->_objectManager->create($this->_modelClass)->setId($id)->delete();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $error = true;
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $error = true;
            $this->messageManager->addException($e, __('We can\'t delete '.strtolower($this->_getModel(false)->getOwnTitle()).' right now. '.$e->getMessage()));
        }

        if (!$error) {
            $this->messageManager->addSuccess(
                __($this->_getModel(false)->getOwnTitle(count($ids) > 1).' have been deleted.')
            );
        }

        $this->_redirect('*/*');
    }

    protected function _massStatusAction()
    {
        $ids = $this->getRequest()->getParam($this->_idKey);
        
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $model = $this->_getModel(false);

        $error = false;

        try {
            $status = $this->getRequest()->getParam('status');
            $statusFieldName = $this->_statusField;

            if (is_null($status)) {
                throw new \Exception(__('Parameter "Status" missing in request data.'));
            }

            if (is_null($statusFieldName)) {
                throw new \Exception(__('Status Field Name is not specified.'));
            }

            foreach($ids as $id) {
                $this->_objectManager->create($this->_modelClass)
                    ->load($id)
                    ->setData($this->_statusField, $status)
                    ->save();
            }

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $error = true;
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $error = true;
            $this->messageManager->addException($e, __('We can\'t change status of '.strtolower($model->getOwnTitle()).' right now. '.$e->getMessage()));
        }

        if (!$error) {
            $this->messageManager->addSuccess(
                __($model->getOwnTitle(count($ids) > 1).' status have been changed.')
            );
        }

        $this->_redirect('*/*');

    }

    protected function _configAction()
    {
        $this->_redirect('admin/system_config/edit', ['section' => $this->_configSection()]);
    }

    protected function _setFormData($data = null)
    {
        $this->_getSession()->setData($this->_formSessionKey,
            is_null($data) ? $this->getRequest()->getParams() : $data);

        return $this;
    }

    protected function _getRegistry()
    {
        if (is_null($this->_coreRegistry)) {
            $this->_coreRegistry = $this->_objectManager->get('\Magento\Framework\Registry');
        }
        return $this->_coreRegistry;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed($this->_allowedKey);
    }

    protected function _getModel($load = true)
    {
        if (is_null($this->_model)) {
            $this->_model = $this->_objectManager->create($this->_modelClass);

            $id = (int)$this->getRequest()->getParam($this->_idKey);
            if ($id && $load) {
                $this->_model->load($id);
            }
        }
        return $this->_model;
    }
}