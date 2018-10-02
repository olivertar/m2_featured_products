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

namespace Orangecat\Featureds\Block\Widget;

use Magento\Catalog\Api\ProductRepositoryInterface;

class Featuredproduct extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    const DEFAULT_PRODUCTS_COUNT = 10;
    
    protected $_template = 'Orangecat_Featureds::widget/featuredproduct.phtml';
     
    protected $_featuredFactory;
    
    protected $_featured;

    protected $reviewRenderer;

    protected $_cartHelper;

    protected $urlHelper;
    
    protected $imageBuilder;

    protected $_wishlistHelper;

    protected $_compareProduct;
    
    public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Framework\Url\Helper\Data $urlHelper,
            \Magento\Catalog\Block\Product\Context $contextproduct,
            \Orangecat\Featureds\Model\Featured $featured,
            \Orangecat\Featureds\Model\FeaturedFactory $featuredFactory,
            array $data = []
    )
    {
        $this->_featured = $featured;
        $this->_featuredFactory = $featuredFactory;
        $this->_cartHelper = $contextproduct->getCartHelper();
        $this->imageBuilder = $contextproduct->getImageBuilder();
        $this->reviewRenderer = $contextproduct->getReviewRenderer();
        $this->_compareProduct = $contextproduct->getCompareProduct();
        $this->_wishlistHelper = $contextproduct->getWishlistHelper();
        $this->urlHelper = $urlHelper;
        parent::__construct($context, $data);
    }

    public function getFeaturedProduct(){
        $limit = $this->getProductLimit();
        if (!$this->hasData('featured') && $this->getData('blockid')) {
             $featured = $this->_featuredFactory->create();
             $featured->setId($this->getData('blockid'));
             $info = $featured->load($this->getData('blockid'));
             if($info->getIsActive() == 1){
                 $this->setData('info', $info);
                 $related = $featured->getRelatedProducts();
                 $related->setPageSize($limit);
                 $this->setData('featured', $related);
             }
        }
        return $this->getData('featured');
    }
   
    public function getProductLimit() {
        if($this->getData('blockid')==''){
            return self::DEFAULT_PRODUCTS_COUNT;
        }else{
            $featured = $this->_featuredFactory->create();
            $qty = $featured->load($this->getData('blockid'))->getItemqty();
            if($qty != ''){
                return $qty;
            }
        }
        return self::DEFAULT_PRODUCTS_COUNT;
    }

    public function getAddToCartUrl($product, $additional = [])
    {
            return $this->_cartHelper->getAddUrl($product, $additional);
    }

    public function getAddToCartPostParams(\Magento\Catalog\Model\Product $product)
    {
        $url = $this->getAddToCartUrl($product);
        return [
                'action' => $url,
                'data' => [
                        'product' => $product->getEntityId(),
                        \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED =>
                        $this->urlHelper->getEncodedUrl($url),
                ]
        ];
    }

    public function isRedirectToCartEnabled()
    {
        return $this->_scopeConfig->getValue(
                'checkout/cart/redirect_to_cart',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getPriceRender();
    
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                    \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                    $product,
                    [
                            'include_container' => true,
                            'display_minimal_price' => true,
                            'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST
                    ]
            );
        }

        return $price;
    }

    protected function getPriceRender()
    {
        return $this->getLayout()->getBlock('product.price.render.default');
    }
    
    public function getProductDetailsHtml(\Magento\Catalog\Model\Product $product)
    {
        $renderer = $this->getDetailsRenderer($product->getTypeId());
        if ($renderer) {
            $renderer->setProduct($product);
            return $renderer->toHtml();
        }
        return '';
    }

    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
        ->setImageId($imageId)
        ->setAttributes($attributes)
        ->create();
    }

    public function getAddToWishlistParams($product)
    {
        return $this->_wishlistHelper->getAddParams($product);
    }

    public function getAddToCompareUrl()
    {
        return $this->_compareProduct->getAddUrl();
    }
    
}