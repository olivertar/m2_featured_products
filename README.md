# M2 Featured Products Module

This module for Magento 2 allows you to create groups of products manually from the backend and display them in the frontend. (Homepage, categories, product pages, cms pages, etc etc)

##Quick Guide

You can add product groups from: Content > Product Blocks

You have different options to display a block of featured products on your site:

The first step will always be to create a block of featured products. Content > Product Blocks

###- Using Magento Widgets

You can add a new widget from Content > Widget

You must select the widget "Featured Products", after selecting the all options, under the widget options tab, you can select the block product that you want to display.

you can know more about widgets magento here

###- Include using wysiwyg editor

If the editor shows you the "Insert Widget" button your able to include a block of featured products.

Using this method you can include a block of featured products in CMS pages or CMS Blocks.

###- Layout Update XML (advanced)

From some parts of the admin panel you can include a block of featured products using XML code.

- CMS pages: "Layout Update XML" under "Design" tab.

- Category: "Custom Layout Update" under "Custom Design" tab.

- Product: "Custom Layout Update" under "Advanced Settings > Design" tab.

The code that you must use is this:

<referenceContainer name="CONTAINER">
      <block class="Orangecat\Featureds\Block\Widget\Featuredproduct" name="NAME" template="widget/featuredproduct.phtml">
           <arguments>
                <argument name="blockid" xsi:type="string">ID</argument>
           </arguments>
      </block>
</referenceContainer>

CONTAINER: It is the place (reference name) where you want to install the block

NAME: You should choose a name for this xml node. The name must be unique.

ID: It is the ID of the block of featured products that you want to show. You can find the ID in Content > Product Blocks


###- Layout XML file (advanced)

if you have enough knowledge, you can also insert XML code directly in the XML files from your theme.

The code that you must use is this:

<referenceContainer name="CONTAINER">
      <block class="Orangecat\Featureds\Block\Widget\Featuredproduct" name="NAME" template="widget/featuredproduct.phtml">
           <arguments>
                <argument name="blockid" xsi:type="string">ID</argument>
           </arguments>
      </block>
</referenceContainer>

CONTAINER: It is the place (reference name) where you want to install the block

NAME: You should choose a name for this xml node. The name must be unique.

ID: It is the ID of the block of featured products that you want to show. You can find the ID in Content > Product Blocks

NOTE: we do not recommend this method because if the ID of your block changed you must edit the file to change it.


## Support
- This module requires for its operation "M2 Base module" (https://github.com/olivertar/m2_base_module)
- This module supports Magento 2 up to version **2.2.5**. It might work on more recent versions, but we cannot make any guarantees.

## Donation
If you have used this module consider the possibility of making a donation

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=BJGDM4EZMETKQ)


##Screenshots

![ScreenShot](https://github.com/olivertar/m2_featured_products/blob/master/screen-shot/featureds_grid.png)
<br/><br/>
![ScreenShot](https://github.com/olivertar/m2_featured_products/blob/master/screen-shot/featureds_edit_tab_general.png)
<br/><br/>
![ScreenShot](https://github.com/olivertar/m2_featured_products/blob/master/screen-shot/featureds_edit_tab_products.png)
<br/><br/>
![ScreenShot](https://github.com/olivertar/m2_featured_products/blob/master/screen-shot/featureds_frontend.png)

