<?php

class Tsg_Exports_Model_Resource_Exports
    extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('tsg_exports/exports','export_id');
    }
}