<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class NowInStore_CatalogBuilder_CategoriesController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $category_collection = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addIsActiveFilter();
        $categories = array();
        foreach($category_collection as $category) {
            $children = $category->getChildrenCategories()->toArray();
            if (count($children) == 0) {
                array_push($categories, array(
                        "id" => $category->getId(),
                        "name" => $category->getName()
                ));
            }
        }
        $jsonData = json_encode($categories);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}