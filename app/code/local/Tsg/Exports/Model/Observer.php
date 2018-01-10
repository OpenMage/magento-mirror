<?php

class Tsg_Exports_Model_Observer
{
    /**
     * Generating all enabled exports
     */
    public function generateAllExports()
    {
        /** @var Tsg_Exports_Model_Exports $model */
        $model = Mage::getModel('tsg_exports/exports');
        $exportsCollection = $model->getCollection()
            ->addFieldToFilter('enable',1);
        foreach($exportsCollection as $export) {
            $model->generateExport($export);
        }
    }

}