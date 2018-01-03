<?php

class Tsg_News_Block_Adminhtml_News_List_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Tsg_News_Block_Adminhtml_News_List_Grid constructor.
     */
    public function __construct()
    {

        parent::__construct();
        $this->setId('news_list_id');
        $this->setDefaultSort('news_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tsg_news/news')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('tsg_news');
        $this->addColumn('news_id', array(
            'header' => $helper->__('News Id'),
            'index' => 'news_id',
        ));
        $this->addColumn('news_title', array(
            'header' => $helper->__('News Title'),
            'index' => 'news_title',
        ));
        $this->addColumn('news_content', array(
            'header' => $helper->__('News Content'),
            'index' => 'news_content',
        ));
        $this->addColumn('news_image', array(
            'header' => $helper->__('News image'),
            'index' => 'news_image',
            'align' => 'center',
            'renderer' => 'tsg_news_block_adminhtml_news_list_renderer_image',
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('news_id' => $row->getId()));
    }

}