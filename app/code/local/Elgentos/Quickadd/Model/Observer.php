<?php
class Elgentos_Quickadd_Model_Observer
{
    public function __construct()
    {

    }

    public function makeDeeplink($observer) {
        if(Mage::getStoreConfig('quickadd/general/enabled')) {
            try {
                $product = $observer->getProduct();
                $prefixURL = Mage::getStoreConfig('quickadd/general/urlprefix');
                $suffixURL = $product->getUrlKey();
                $product->setDeeplink(Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$prefixURL."/".$suffixURL);

                $rewrite = Mage::getModel('core/url_rewrite');
                $rewrite->setStoreId(Mage::app()->getStore()->getId())
                        ->setIdPath($prefixURL."/".$suffixURL)
                        ->setRequestPath($prefixURL."/".$suffixURL)
                        ->setTargetPath('quickadd/index/index/product/'.$product->getId()."/urlkey/".$product->getUrlKey())
                        ->setIsSystem(true)
                        ->save();

            } catch(Exception $e) {
                // also gives exception when path already exists
            }
        }
    }
}