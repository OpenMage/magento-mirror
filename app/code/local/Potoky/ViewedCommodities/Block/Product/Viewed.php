<?php

class Potoky_ViewedCommodities_Block_Product_Viewed extends Mage_Reports_Block_Product_Viewed
{

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        Mage::register('viewed_block', 'engaged');

        parent::_construct();
    }

    /**
     * Prepare to html.
     * Checks if JS block forming has started to perform
     * and due to that renders view ether from the standard template
     * or from JS Block template
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::registry('jsblock_allowed')) {
                return $this->loadFromJs();
        }

        return parent::_toHtml();
    }

    /**
     * Retrieves html for JS Block template
     *
     * @return string
     */
    private function loadFromJs()
    {
        $this->setTemplate('viewedcommodities/commodity_viewed.phtml');
        $html = $this->renderView();
        return $html;
    }
}