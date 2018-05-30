<?php

class Potoky_ViewedCommodities_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Lifetime of JS Block
     *
     * @var int
     */
    private $lifetime;

    /**
     * Extracts sufficient information about the passed in products
     * and returns it in the format expected by JS storage scripts
     *
     *
     * @param Mage_Catalog_Model_Product[] $products
     * @return array
     */
    public function getProductInfo(array $products)
    {
        $prodsInfoArr =[];
        foreach ($products as $product) {
            $productSku = trim($product->getSku());
            $prodsInfoArr[$productSku] = [
                'product_url' => $product->getProductUrl(),
                'image_src'   => Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(50, 50)->setWatermarkSize('30x10')->__toString(),
                'image_alt'   => Mage::helper('core')->escapeHtml($product->getName()),
                'name_link'   => Mage::helper('catalog/output')->productAttribute($product, $product->getName() , 'name')
            ];
        }

        return $prodsInfoArr;
    }

    /**
     * Adds needed Java Script to a page and creates control cookie
     *
     * @param Mage_Core_Model_Layout $layout
     * @param string $template
     * @param string $cookieVal
     */
    public function addJsVC(Mage_Core_Model_Layout $layout, $template = null, $cookieVal = null) {
        $layout->getBlock('head')->addJs('local/storage.js');

        if (!$template) {
            return;
        }
        $endBlock = $layout->createBlock(
            'Mage_Core_Block_Template',
            'localstorage_rendering',
            array('template' => 'viewedcommodities/' . $template,
            ));
        $layout->getBlock('before_body_end')->append($endBlock);

        if (!$cookieVal) {
            return;
        }
        setcookie('viewed_commodities', $cookieVal, 0,'/');
    }

    /**
     * Checks whether Viewed products may be loaded from JS block.
     * Unsets session variable 'viewed_commodities' if expired.
     *
     * @return bool
     */
    public function jsGenerationAllowed()
    {
        if (!isset($_SESSION['viewed_commodities'])) {

            return false;
        }
        if ((int) $_SESSION['viewed_commodities'] - time() < 0) {
            unset($_SESSION['viewed_commodities']);

            return false;
        }
        if (isset($_COOKIE['viewed_commodities'])) {

            return false;
        }

        return true;
    }

    /**
     * Retrieves the lifetime of the JS Block.
     *
     * @return int $this->lifetime
     */
    public function getLifetime()
    {
        if (!isset($this->lifetime)) {
            $lifetime = Mage::getStoreConfig('catalog/lifetime_vc/viewedcommodities_lifetime');
            $this->lifetime = $lifetime;
        }

        return $this->lifetime;
    }
}
