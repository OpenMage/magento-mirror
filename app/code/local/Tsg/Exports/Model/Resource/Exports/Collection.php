<?php

class Tsg_Exports_Model_Resource_Exports_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('tsg_exports/exports');
    }

    /**
     * @param $ids
     * @return $this
     */
    public function addIdsFilter($ids)
    {
        if (is_array($ids)) {
            $this->addFieldToFilter('export_id', array('in' => $ids));
        } elseif (is_numeric($ids) || is_string($ids)) {
            $this->addFieldToFilter('export_id', $ids);
        }
        return $this;
    }
}