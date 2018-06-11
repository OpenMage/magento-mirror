<?php

class Potoky_ViewedProducts_Helper_Product extends Mage_Core_Helper_Abstract
{
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
}