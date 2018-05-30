<?php

class Potoky_ViewedCommodities_Model_Observer
{
    /**
     * Observes the loaded page and ads necessary JS and scripts if
     * the localstorage is empty of viewed products.
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function pageWatch(Varien_Event_Observer $observer)
    {
        if (Mage::helper('viewedcommodities')->getLifetime() == false) {

            return;
        }
        $layout = $observer->getEvent()->getLayout();
        $viewedPresent = (Mage::registry('viewed_block')) ? true : false;
        $viewPresent = in_array('catalog_product_view', $layout->getUpdate()->getHandles());
        if (($viewedPresent || $viewPresent) && !Mage::helper('viewedcommodities')->jsGenerationAllowed()) {
            $cookieVal = (isset($_COOKIE['viewed_commodities'])) ? null : 'reset';
            Mage::helper('viewedcommodities')->addJsVC($layout, 'storage_execution.phtml', $cookieVal);
            return;
        }
        elseif ($viewPresent) {
                Mage::register('viewed_commodity',$this->getProdInfo());
                Mage::helper('viewedcommodities')->addJsVC($layout, 'gatherer.phtml', 'add');
        }
        elseif ($viewedPresent) {
            Mage::register('jsblock_allowed', true);
            Mage::helper('viewedcommodities')->addJsVC($layout);
        }
    }

    /**
     * Retrieves necessary information about the product being viewed
     * to be used in JS script that will add it to the "localStorage"
     *
     * @return array
     */
    private function getProdInfo()
    {
        $prodsInfoArr = Mage::helper('viewedcommodities')
            ->getProductInfo([Mage::registry('current_product')]);
        $sku = array_keys($prodsInfoArr)[0];

        return [
            'sku'         => $sku,
            'productInfo' => Mage::helper('core')->jsonEncode($prodsInfoArr[$sku])
        ];
    }
}
