<?php

class Tsg_News_Model_Resource_News
    extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('tsg_news/list', 'news_id');
    }

}