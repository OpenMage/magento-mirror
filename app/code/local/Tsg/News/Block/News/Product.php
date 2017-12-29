<?php

class Tsg_News_Block_News_Product
    extends Mage_Core_Block_Template
{
    /**
     * This function is rendering link for product news list
     *
     * @return null|string
     */
    public function getNewsLink()
    {
        $product = Mage::registry('current_product');
        $news = $product->getNewslist();
        $link = null;
        if (!empty($news)) {
            $link = $this->getBaseUrl() . 'news/index/newslist/product/' . $product->getId();
        }
        return $link;
    }

}