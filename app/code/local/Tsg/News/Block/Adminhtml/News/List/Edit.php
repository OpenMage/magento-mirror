<?php

class Tsg_News_Block_Adminhtml_News_List_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Tsg_News_Block_Adminhtml_News_List_Edit constructor.
     */
    public function _construct()
    {
        parent::_construct();
        $this->_blockGroup = 'tsg_news';
        $this->_controller = 'adminhtml_news_list';
        $this->_headerText = Mage::helper('tsg_news')->__('New News Form');
    }

}