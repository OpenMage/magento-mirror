<?php

class Tsg_News_Block_Adminhtml_News_List
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Tsg_News_Block_Adminhtml_News_List constructor.
     */
    public function __construct()
    {
        $helper = Mage::helper('tsg_news');
        $this->_blockGroup = 'tsg_news';
        $this->_controller = 'adminhtml_news_list';
        $this->_headerText = $helper->__('News - List');
        $this->_addButtonLabel = $helper->__('Add new News');
        parent::__construct();
    }

}