<?php

class Tsg_News_Model_Resource_News_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('tsg_news/news');
    }

    /**
     * @param $ids
     * @return $this
     */
    public function addIdsFilter($ids)
    {
        if (is_array($ids)) {
            $this->addFieldToFilter('news_id', array('in' => $ids));
        } elseif (is_numeric($ids) || is_string($ids)) {
            $this->addFieldToFilter('news_id', $ids);
        }
        return $this;
    }
}