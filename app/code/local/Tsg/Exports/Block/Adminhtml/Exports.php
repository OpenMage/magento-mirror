<?php

class Tsg_Exports_Block_Adminhtml_Exports
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function _construct()
    {
        $this->_blockGroup = 'tsg_exports';
        $this->_controller = 'adminhtml_exports';
        $this->_headerText = $this->__('Exports');
        parent::_construct();
    }
}