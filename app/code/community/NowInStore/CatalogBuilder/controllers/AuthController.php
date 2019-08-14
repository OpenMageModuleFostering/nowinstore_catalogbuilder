<?php
class NowInStore_CatalogBuilder_AuthController extends Mage_Core_Controller_Front_Action
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

        $baseUrl = urlencode(Mage::getBaseUrl());
        $hostname = urlencode(Mage::app()->getFrontController()->getRequest()->getHttpHost());
        $address = urlencode(str_replace("\r\n", "<br/>", Mage::getStoreConfig('general/store_information/address')));
        $email = urlencode(Mage::getStoreConfig('trans_email/ident_general/email'));
        $businessName = urlencode(Mage::getStoreConfig('general/store_information/name'));
        $name = urlencode(Mage::getStoreConfig('trans_email/ident_general/name'));
        $phone = urlencode(Mage::getStoreConfig('general/store_information/phone'));
        $version = urlencode(Mage::getVersion());
        $destinationUrl = "https://www.nowinstore.com/auth/magento/callback?baseUrl=$baseUrl&hostname=$hostname&address=$address&email=$email&businessName=$businessName&name=$name&phone=$phone&version=$version";
        $this->getResponse()->setRedirect($destinationUrl);
    }
}
