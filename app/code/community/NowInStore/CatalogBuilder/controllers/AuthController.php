<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
class NowInStore_CatalogBuilder_AuthController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $email = Mage::getStoreConfig('trans_email/ident_general/email');
        $baseUrl = urlencode (Mage::getBaseUrl());
        $this->getResponse()->setRedirect("https://www.nowinstore.com/auth/magento/callback?baseUrl=$baseUrl");
    }
}
