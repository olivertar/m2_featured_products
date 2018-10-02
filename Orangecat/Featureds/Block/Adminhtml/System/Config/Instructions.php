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

namespace Orangecat\Featureds\Block\Adminhtml\System\Config;

use Magento\Store\Model\ScopeInterface;

class Instructions extends \Magento\Config\Block\System\Config\Form\Field
{

    protected $moduleList;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {

        $html = '<div style="padding:10px;background-color:#f8f8f8;border:1px solid #ddd;margin-bottom:7px;">
                
<p>You have different options to display a <strong>block of featured products</strong> on your site:</p>

<p>The <strong>first step</strong> will always be to create a <strong>block of featured products</strong>. Content > Product Blocks</p>

<br/>
<h2><strong>- Using Magento Widgets</strong></h2>
<p>You can add a new widget from Content > Widget</p>
<p>You must select the widget "Featured Products", after selecting the all options, under the widget options tab, you can select the block product that you want to display.</p>

<p>you can know more about widgets magento <a href="http://docs.magento.com/m2/ee/user_guide/cms/widgets.html" target="_blank">here</a></p>
<br/>
<h2><strong>- Include using wysiwyg editor</strong></h2>

<p>If the editor shows you the "Insert Widget" button your able to include a <strong>block of featured products</strong>.</p>
<p>Using this method you can include a <strong>block of featured products</strong> in CMS pages or CMS Blocks.</p>
<br/>

<h2><strong>- Layout Update XML</strong> (advanced)</h2>

<p>From some parts of the admin panel you can include a <strong>block of featured products</strong> using XML code.</p>

<p>- CMS pages: "Layout Update XML" under "Design" tab.</p>
<p>- Category: "Custom Layout Update" under "Custom Design" tab.</p>
<p>- Product: "Custom Layout Update" under "Advanced Settings  > Design" tab.</p>

<p>The code that you must use is this:<p>
<p>
&lt;referenceContainer name="<strong>CONTAINER</strong>"&gt;<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;     &lt;block class="Orangecat\Featureds\Block\Widget\Featuredproduct" name="<strong>NAME</strong>" template="widget/featuredproduct.phtml"&gt;<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;          &lt;arguments><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;               &lt;argument name="blockid" xsi:type="string"><strong>ID</strong>&lt;/argument&gt;<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;          &lt;/arguments&gt;<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;     &lt;/block&gt;<br/>
&lt;/referenceContainer&gt;

</p>
<br/>
<p><strong>CONTAINER:</strong> It is the place (reference name) where you want to install the block<p>
<p><strong>NAME:</strong> You should choose a name for this xml node. The name must be unique.</p>
<p><strong>ID:</strong> It is the ID of the block of featured products that you want to show. You can find the ID in Content > Product Blocks</p>
<br/>
<p>you can know more about Magento XML Layouts <a href="http://devdocs.magento.com/guides/v2.0/frontend-dev-guide/layouts/layout-overview.html" target="_blank">here</a></p>
<br/>

<h2><strong>- Layout XML file</strong> (advanced)</h2>
<p>if you have enough knowledge, you can also insert XML code directly in the XML files from your theme.</p>
<p>The code that you must use is this:<p>
<p>
&lt;referenceContainer name="<strong>CONTAINER</strong>"&gt;<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;     &lt;block class="Orangecat\Featureds\Block\Widget\Featuredproduct" name="<strong>NAME</strong>" template="widget/featuredproduct.phtml"&gt;<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;          &lt;arguments><br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;               &lt;argument name="blockid" xsi:type="string"><strong>ID</strong>&lt;/argument&gt;<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;          &lt;/arguments&gt;<br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;     &lt;/block&gt;<br/>
&lt;/referenceContainer&gt;

</p>
<br/>
<p><strong>CONTAINER:</strong> It is the place (reference name) where you want to install the block<p>
<p><strong>NAME:</strong> You should choose a name for this xml node. The name must be unique.</p>
<p><strong>ID:</strong> It is the ID of the block of featured products that you want to show. You can find the ID in Content > Product Blocks</p>
<br/>
<p><strong>NOTE:</strong> we do not recommend this method because if the ID of your block changed you must edit the file to change it.</p>
<p>you can know more about Magento XML Layouts <a href="http://devdocs.magento.com/guides/v2.0/frontend-dev-guide/layouts/layout-overview.html" target="_blank">here</a></p>
<br/>
                
                
                </div>';

        return $html;
    }

}
