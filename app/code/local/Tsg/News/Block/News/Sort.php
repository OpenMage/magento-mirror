<?php

class Tsg_News_Block_News_Sort
    extends Mage_Catalog_Block_Product_List_Toolbar
{
    public function getAvailableOrders()
    {
        return array(
            'created_at' =>'Date',
            'news_priority' => 'Priority',
        );
    }

}