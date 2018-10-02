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

namespace Orangecat\Featureds\Controller\Adminhtml\Featured;

use Orangecat\Featureds\Model\Featured;

class Save extends \Orangecat\Featureds\Controller\Adminhtml\Featured
{
    protected function _beforeSave($model, $request)
    {
        $dateFilter = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\Filter\Date');
        $data = $model->getData();
        
        $inputFilter = new \Zend_Filter_Input(
                ['publish_time' => $dateFilter],
                [],
                $data
        );

        $data = $inputFilter->getUnescaped();
        $model->setData($data);

        if ($links = $request->getPost('links')) {
            $jsHelper = $this->_objectManager->create('Magento\Backend\Helper\Js');

            $links = is_array($links) ? $links : [];
            $linkTypes = ['relatedproducts'];
            foreach ($linkTypes as $type) {
                if (isset($links[$type])) {
                    $links[$type] = $jsHelper->decodeGridSerializedInput($links[$type]);

                    $model->setData($type.'_links', $links[$type]);
                }
            }
        }

    }

}
