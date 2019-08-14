<?php
class NowInStore_CatalogBuilder_ProfileController extends Mage_Core_Controller_Front_Action
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

        $hostname = Mage::app()->getFrontController()->getRequest()->getHttpHost();
        $address = str_replace("\r\n", "<br/>", Mage::getStoreConfig('general/store_information/address'));
        $jsonData = json_encode(array(
            "business_name" => Mage::getStoreConfig('general/store_information/name'),
            "name" => Mage::getStoreConfig('trans_email/ident_general/name'),
            "email" => Mage::getStoreConfig('trans_email/ident_general/email'),
            "baseUrl" =>  Mage::getBaseUrl(),
            "phone" => Mage::getStoreConfig('general/store_information/phone'),
            "address" => $address
        ));
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($jsonData);
    }
}
