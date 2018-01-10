<?php

class Tsg_Exports_Block_Adminhtml_Exports_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tsg_export_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('tsg_exports')->__('Export Information'));
    }

    protected function _prepareLayout()
    {
        $this->addTab('categories', array(
            'label' => Mage::helper('tsg_exports')->__('Категории'),
            'url' => $this->getUrl('*/*/categories', array('_current' => true)),
            'class' => 'ajax',
        ));
        return parent::_prepareLayout();
    }

}