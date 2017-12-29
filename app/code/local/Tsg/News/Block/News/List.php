<?php

class Tsg_News_Block_News_List
    extends Mage_Core_Block_Template
{

    /**
     * Tsg_News_Block_News_List constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $productId = $this->getRequest()->getParam('product');
        if ($productId === null) {
            /** @var Tsg_News_Model_Resource_News $collection */
            $collection = Mage::getModel('tsg_news/news')->getCollection();
        } else {
            $collection = $this->getProductNews($productId);
        }
        $collection = $this->sortNewsByOrder($collection);
        $this->setCollection($collection);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var Mage_Page_Block_Html $pager */
        $pager = $this->getLayout()->createBlock('page/html_pager', 'list.pager');
        $pager->setAvailableLimit(array(5 => 5, 10 => 10, 20 => 20, 'all' => 'all'));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }

    /**
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param $newsContent
     * @param $newsId
     * @return string
     */
    public function renderNewsContent($newsContent, $newsId)
    {
        $link = $this->getUrl('*/*/read/news/' . $newsId);
        $content = substr($newsContent, 0, 40) . " <a href=" . $link . ">Read the News </a>";
        return $content;
    }

    /**
     * @param $productId
     * @return Tsg_News_Model_Resource_News_Collection
     */
    public function getProductNews($productId)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        $newsIds = explode(',', $product->getNewslist());
        /** @var Tsg_News_Model_Resource_News_Collection $collection */
        $collection = Mage::getModel('tsg_news/news')->getCollection()->addIdsFilter($newsIds);
        return $collection;
    }

    /**
     * This function is adding order by params to news collection
     *
     * @param $collection
     * @return Tsg_News_Model_Resource_News_Collection
     */
    public function sortNewsByOrder($collection)
    {
        $order = array(
            'dir' => 'asc',
            'order' => 'created_at',
        );
        $params = $this->getRequest()->getParams();
        if (isset ($params['dir'], $params['order'])) {
            $order = array(
                'dir' => $params['dir'],
                'order' => $params['order'],
            );
        }
        /** @var Tsg_News_Model_Resource_News_Collection $collection */
        $collection->setOrder($order['order'], $order['dir']);
        return $collection;


    }

}