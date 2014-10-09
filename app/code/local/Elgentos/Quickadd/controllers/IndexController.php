<?php
class Elgentos_Quickadd_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        if(Mage::getStoreConfig('quickadd/general/enabled')) {
            $params = $this->getRequest()->getParams();
            if(isset($params['urlkey'])) {
                $session = Mage::getModel('core/session', array('name' => 'frontend'));
                $cart = Mage::getModel('checkout/cart');

                // Set variables
                $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load(Mage::getModel('catalog/product')->loadByAttribute('url_key', $params['urlkey'])->getId());

                if(isset($params['qty'])) {
                    $productParams['qty'] = $params['qty'];
                } else {
                    $productParams['qty'] = Mage::getStoreConfig('quickadd/general/defaultqty');
                }
                $params = $this->getRequest()->getParams();
                foreach($params as $param=>$value) {
                    if($param=='product' || $param=='urlkey') continue;
                    $productParams[$param] = $value;
                }
                try {
                    // Add to cart and save
                    $cart->addProduct($product, $productParams);
                    $cart->save();

                    // Set success message
                    $message = Mage::getStoreConfig('quickadd/general/message');
                    $message = str_replace(":product:",$product->getName(),$message);
                    Mage::getSingleton('core/session')->addSuccess($message);

                    // Redirect
                    $redirect = Mage::getStoreConfig('quickadd/general/redirect');
                    if(empty($redirect)) $redirect = "checkout/cart";
                    if($redirect==":home:") $redirect = "/";
                    if($redirect==":product:") $redirect = $product->getUrlPath();
                    $this->_redirect($redirect);
                } catch(Exception $e) {
                    $message = Mage::getStoreConfig('quickadd/general/errormessage');
                    $message = str_replace(":product:",$product->getName(),$message);
                    Mage::getSingleton('core/session')->addError($message);
                    $this->_redirect('/');
                }
            } else {
                $message = Mage::getStoreConfig('quickadd/general/errormessage');
                $message = str_replace(":product:",$product->getName(),$message);
                Mage::getSingleton('core/session')->addError($message);
                $this->_redirect('/');
            }
        } else {
            $this->_redirect('/');
        }
    }
}