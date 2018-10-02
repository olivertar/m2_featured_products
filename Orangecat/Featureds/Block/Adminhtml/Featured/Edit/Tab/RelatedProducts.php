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

namespace Orangecat\Featureds\Block\Adminhtml\Featured\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class RelatedProducts extends Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    protected $_coreRegistry = null;

    protected $_status;

    protected $_productCollectionFactory;

    protected $_visibility;

    protected $_websiteFactory;
    
    protected $_featured;
    
    protected $request;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
    	\Magento\Framework\App\Request\Http $request,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
    	\Orangecat\Featureds\Model\Featured $featured,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_status = $status;
        $this->request = $request;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_featured = $featured;
        $this->_visibility = $visibility;
        $this->_websiteFactory = $websiteFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('related_products_section');
        $this->setDefaultSort('featured_id');
        $this->setUseAjax(true);
        if ($this->getFeatured() && $this->getFeatured()->getId()) {
        	$this->setDefaultFilter(['in_products' => 1]);
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    public function getFeatured()
    {
        return $this->_coreRegistry->registry('current_model');
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection_website_column()
    {
        $featured = $this->getFeatured();
        $collection = $this->_productCollectionFactory->create()->groupByAttribute('entity_id')
            ->addAttributeToSelect('*')
            ->joinField(
                'websites',
                'catalog_product_website',
                'website_id',
                'product_id=entity_id',
                null,
                'left'
            );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareCollection()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function isReadonly()
    {
        return false;
    }

    protected function _prepareColumns()
    {
        if (!$this->isReadonly()) {
            $this->addColumn(
                'in_products',
                [
                    'type' => 'checkbox',
                    'name' => 'in_products',
                    'values' => $this->_getSelectedProducts(),
                    'align' => 'center',
                    'index' => 'entity_id',
                    'header_css_class' => 'col-select',
                    'column_css_class' => 'col-select'
                ]
            );
        }

        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku'
            ]
        );

        $this->addColumn(
            'visibility',
            [
                'header' => __('Visibility'),
                'index' => 'visibility',
                'type' => 'options',
                'options' => $this->_visibility->getOptionArray(),
                'header_css_class' => 'col-visibility',
                'column_css_class' => 'col-visibility'
            ]
        );

        /*if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'websites',
                [
                    'header' => __('Websites'),
                    'sortable' => false,
                    'index' => 'websites',
                    'type' => 'options',
                    'options' => $this->_websiteFactory->create()->getCollection()->toOptionHash(),
                    'header_css_class' => 'col-websites',
                    'column_css_class' => 'col-websites'
                ]
            );
        }*/

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->_status->getOptionArray(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status',
                'frame_callback' => array(
                    $this->getLayout()->createBlock('Orangecat\Featureds\Block\Adminhtml\Grid\Column\Statuses'),
                    'decorateStatus'
                ),
            ]
        );

        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'name' => 'position',
                'type' => 'number',
                'validate_class' => 'validate-number',
                'index' => 'position',
                'editable' => true,
                'edit_only' => false,
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-position',
                'column_css_class' => 'col-position'
            ]
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getData(
            'grid_url'
        ) ? $this->getData(
            'grid_url'
        ) : $this->getUrl(
            'featureds/featured/relatedProductsGrid',
            ['_current' => true]
        );
    }
    
    public function _getSelectedProducts()
    {
        $id = $this->request->getParam('id');
        
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->getSelect()->joinLeft(
            ['rl' => 'featured_relatedproduct'],
            'e.entity_id = rl.related_id'
            )->where(
                'rl.featured_id = ?',
                $id
                );
            $products = [];
            foreach ($collection as $product) {
                $products[] = $product->getId();
            }
           
            return $products;
    }

    public function getSelectedRelatedProducts()
    {
    	$id = $this->request->getParam('id');
    	
    	$collection = $this->_productCollectionFactory->create()->groupByAttribute('entity_id')
    	->addAttributeToSelect('*')
    	->joinField(
    			'websites',
    			'catalog_product_website',
    			'website_id',
    			'product_id=entity_id',
    			null,
    			'left'
    	);
    	 
    	$collection->getSelect()->joinLeft(
    			['rl' => 'featured_relatedproduct'],
    			'e.entity_id = rl.related_id'
    	)->where(
    			'rl.featured_id = ?',
    			$id
    	);
    	$products = [];
    	foreach ($collection as $product) {
    		$products[$product->getId()] = ['position' => $product->getPosition()];
    	}
        return $products;
    }

    public function getTabLabel()
    {
        return __('Related Products');
    }

    public function getTabTitle()
    {
        return __('Related Products');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
