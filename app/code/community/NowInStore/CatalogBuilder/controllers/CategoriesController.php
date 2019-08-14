<?php
class NowInStore_CatalogBuilder_CategoriesController extends Mage_Core_Controller_Front_Action
{
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

        $category_collection = Mage::getModel('catalog/category')
                ->getCollection()
                ->addAttributeToSelect('*')
                ->addIsActiveFilter();
        $categories = array();
        foreach($category_collection as $category) {
            array_push($categories, array(
                "id" => $category->getId(),
                "name" => $category->getName()
            ));
        }
        $jsonData = json_encode($categories);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}
