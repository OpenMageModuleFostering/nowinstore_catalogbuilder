<?php
class NowInStore_CatalogBuilder_CategoriesController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $category_collection = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addIsActiveFilter();
        $categories = [];
        foreach($category_collection as $category) {
            $children = $category->getChildrenCategories()->toArray();
            if (count($children) == 0) {
                array_push($categories, [
                        "id" => $category->getId(),
                        "name" => $category->getName()
                ]);
            }
        }
        $jsonData = json_encode($categories);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}