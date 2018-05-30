<?php

class Potoky_ViewedCommodities_StorageController extends Mage_Core_Controller_Front_Action
{
    /**
     * Gatheres information about the Viewed Products on the server and
     * pass it to JS scripts via AJAX
     *
     * @return void
     */
    public function gatherAction()
    {
        $products = Mage::getModel('reports/product_index_viewed')
            ->getCollection()
            ->addAttributeToSelect(['name', 'thumbnail', 'url_key'])
            ->addIndexFilter();
        $prodsInfoArr = Mage::helper('viewedcommodities')->getProductInfo($products);
        $lifeTime = Mage::helper('viewedcommodities')->getLifetime();
        $expiry = time() + $lifeTime;
        $_SESSION['viewed_commodities'] = $expiry;
        $response = ['products_info' => $prodsInfoArr, 'expiry' => $expiry * 1000];
        setcookie('viewed_commodities', 'engage', 0,'/');
        echo Mage::helper('core')->jsonEncode($response);
    }
}