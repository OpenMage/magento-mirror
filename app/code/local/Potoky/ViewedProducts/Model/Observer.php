<?php

class Potoky_ViewedProducts_Model_Observer
{
    /**
     * Observes the loaded page and ads necessary JS and scripts if
     * the localstorage needs a renewal of viewed product or products
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function pageWatch(Varien_Event_Observer $observer)
    {
        if (Mage::helper('viewedproducts/lifetime')->getLifetime() == null) {

            return;
        }
        $layout = $observer->getEvent()->getLayout();
        $viewedPresent = (Mage::registry('viewed_block')) ? true : false;
        $viewPresent = in_array('catalog_product_view', $layout->getUpdate()->getHandles());
        if (($viewedPresent || $viewPresent) && !$this->isJsGenerationAllowed()) {
            $cookieVal = (Mage::getModel('core/cookie')->get('viewed_products')) ? null : 'reset';
            $this->addNeededJsToPage($layout, 'storage_execution.phtml', $cookieVal);
            return;
        }
        elseif ($viewPresent) {
                Mage::register('viewed_product',$this->getProdInfo());
                $this->addNeededJsToPage($layout, 'gatherer.phtml', 'add');
        }
        elseif ($viewedPresent) {
            Mage::register('jsblock_allowed', true);
            $this->addNeededJsToPage($layout);
        }
    }

    /**
     * Observes if any changes were made concerning Catalog section
     * in System Config and if so provides rewrites its timestamp field anew
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function systemConfigWatch(Varien_Event_Observer $observer)
    {
        Mage::getModel('core/config')->saveConfig('catalog/js_viewed_products/timestamp', time());
    }

    /**
     * Retrieves necessary information about the product being viewed
     * to be used in JS script that will add it to the "localStorage"
     *
     * @return array
     */
    private function getProdInfo()
    {
        $prodsInfoArr = Mage::helper('viewedproducts/product')
            ->getProductInfo([Mage::registry('current_product')]);
        $sku = array_keys($prodsInfoArr)[0];

        return [
            'sku'         => $sku,
            'productInfo' => Mage::helper('core')->jsonEncode($prodsInfoArr[$sku])
        ];
    }

    /**
     * Adds needed Java Script to a page and creates control cookie
     *
     * @param Mage_Core_Model_Layout $layout
     * @param string $template
     * @param string $cookieVal
     */
    private function addNeededJsToPage(Mage_Core_Model_Layout $layout, $template = null, $cookieVal = null) {
        $layout->getBlock('head')->addJs('local/storage.js');

        if (!$template) {
            return;
        }
        $endBlock = $layout->createBlock(
            'Mage_Core_Block_Template',
            'localstorage_rendering',
            array('template' => 'viewedproducts/' . $template,
            ));
        $layout->getBlock('before_body_end')->append($endBlock);

        if (!$cookieVal) {
            return;
        }
        Mage::getModel('core/cookie')->set('viewed_products', $cookieVal, 0, '/', null, null, false);
    }

    /**
     * Checks whether Viewed products may be loaded from JS block.
     * Unsets session variable 'viewed_products' if expired.
     *
     * @return bool
     */
    private function isJsGenerationAllowed()
    {
        $sessionData = Mage::getSingleton('core/session')->getData('viewed_products');
        if (!$sessionData) {

            return false;
        }

        if ((int) $sessionData['timestamp'] !== (int) Mage::getStoreConfig('catalog/js_viewed_products/timestamp') ||
            (int) $sessionData['expiry'] - time() < 0) {
            Mage::helper('viewedproducts/session')->unsetSessionSetCookieForViewedProducts('reset');

            return false;
        }

        if (Mage::getModel('core/cookie')->get('viewed_products')) {

            return false;
        }

        return true;
    }
}
