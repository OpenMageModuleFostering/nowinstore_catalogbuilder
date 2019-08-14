<?php

class NowInStore_CatalogBuilder_ProductsController extends Mage_Core_Controller_Front_Action
{
    private function count() {

        $product_collection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addExpressionAttributeToSelect('lower_name', 'LOWER({{name}})', array('name'))
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addAttributeToSelect(array('id', 'name', 'sku', 'price', 'group_price', 'image', 'description', 'short_description'));

        $keywords = $_GET['keywords'];
        if (!empty ($keywords)) {
            $product_collection = $product_collection->addAttributeToFilter('lower_name', array('like' => '%' . strtolower($keywords) . '%'));
        }

        $category_id = $_GET['category_id'];
        if (!empty ($category_id)) {
            $product_collection = $product_collection
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id=entity_id', null, 'left')
                ->addAttributeToFilter('category_id', array('in' => $category_id));
        }

        return $product_collection->getSize();
    }

    public function indexAction()
    {
        $debug = $_GET['debug'];
        if ($debug) {
            Mage::setIsDeveloperMode(true);
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            if ($debug == 'all')
                error_reporting(E_ALL);
            if ($debug == 'info')
                 error_reporting(E_ERROR | E_WARNING | E_PARSE);
            if ($debug == 'warning')
                 error_reporting(E_ERROR | E_WARNING);
            if ($debug == 'error')
                 error_reporting(E_ERROR);
        }

        $page = $_GET['page'];
        if (empty($page)) {
            $page = 1;
        }

        $limit = $_GET['limit'];
        if (empty($limit)) {
            $limit = 50;
        }

        $products = array();
        $productsCount = $this->count();
        if ($productsCount > ($page-1)*$limit) {
            $product_collection = Mage::getModel('catalog/product')
                ->getCollection()
    //                            ->addAttributeToFilter('is_active', 1)
                ->addExpressionAttributeToSelect('lower_name', 'LOWER({{name}})', array('name'))
                ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
                ->setPageSize($limit)
                ->setCurPage($page)
                ->addAttributeToSelect(array('id', 'name', 'sku', 'price', 'group_price', 'image', 'description', 'short_description'));

            $keywords = $_GET['keywords'];
            if (!empty ($keywords)) {
                $product_collection = $product_collection->addAttributeToFilter('lower_name', array('like' => '%' . strtolower($keywords) . '%'));
            }

            $category_id = $_GET['category_id'];
            if (!empty ($category_id)) {
                $product_collection = $product_collection
                    ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id=entity_id', null, 'left')
                    ->addAttributeToFilter('category_id', array('in' => $category_id));
            }
            $currency = Mage::app()->getStore()->getCurrentCurrencyCode();
            $group_collection = Mage::getModel('customer/group')->getCollection();
            $wholesaleGroup = null;
            foreach ($group_collection as $group) {
                if ($group->getCode() === 'Wholesale') {
                    $wholesaleGroup = $group;
                }
            }
            foreach ($product_collection as $product) {
                $attributeOptions = array();
                if ($product->isConfigurable()) {
                    $productAttributeOptions = $product->getTypeInstance()->getConfigurableAttributesAsArray($product);
                    foreach ($productAttributeOptions as $productAttribute) {
                        foreach ($productAttribute['values'] as $attribute) {
                            $label = $productAttribute['label'];
                            $valueIndex = $attribute['value_index'];
                            $attributeOptions[$label][$valueIndex] = $attribute['store_label'];
                        }
                    }
                }
                $mainImage = $product->getImageUrl();
                $product->load('media_gallery');
                $mediaGallery =  $product->getMediaGalleryImages();
                $images = array();
                foreach ($mediaGallery as $image) {
                    array_push($images, $image->getUrl());
                }

                if (is_null($product->getImage()) || $product->getImage() == 'no_selection' && count($images) > 0) {
                    $mainImage = $images[0];
                }
                $price = floatval($product->getPrice());
                $wholesalePrice = 0;
                if (!is_null($wholesaleGroup)) {
                    $product->setCustomerGroupId($wholesaleGroup->getId());
                }
                $groupPrices = $product->getGroupPrice();
                if (is_null($groupPrices)) {
                    $attribute = $product->getResource()->getAttribute('group_price');
                    if ($attribute) {
                        $attribute->getBackend()->afterLoad($product);
                        $groupPrices = $product->getData('group_price');
                    }
                }
                if (!is_null($groupPrices) || is_array($groupPrices)) {
                    $wholesalePrice = $groupPrices;
                }

                array_push($products, array(
                    "id" => $product->getId(),
                    "title" => $product->getName(),
                    "sku" => $product->getSku(),
                    "price" => $price,
                    "wholesale_price" => floatval($wholesalePrice),
                    "main_image" => $mainImage,
                    "images" => $images,
                    "description" => $product->getDescription(),
                    "short_description" => $product->getShortDescription(),
                    "thumbnail_image" => (string)Mage::helper('catalog/image')->init($product, 'image')->resize(75),
                    "iso_currency_code" => $currency,
                    "url" => $product->getProductUrl(),
                    "variations" => $attributeOptions
                ));
            }
        }
        $jsonData = json_encode($products);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }

    public function countAction()
    {
        $product_collection = Mage::getModel('catalog/product')
            ->getCollection()
//                            ->addAttributeToFilter('is_active', 1)
            ->addExpressionAttributeToSelect('lower_name', 'LOWER({{name}})', array('name'))
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addAttributeToSelect(array('id', 'name', 'sku', 'price', 'image'));

        $query = $_GET['query'];
        if (!empty ($query)) {
            $product_collection = $product_collection->addAttributeToFilter('lower_name', array('like' => '%' . strtolower($query) . '%'));
        }

        $category_id = $_GET['category_id'];
        if (!empty ($category_id)) {
            $product_collection = $product_collection
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id=entity_id', null, 'left')
                ->addAttributeToFilter('category_id', array('in' => $category_id));
        }
        $jsonData = json_encode(array("count" => $product_collection->count()));
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}
