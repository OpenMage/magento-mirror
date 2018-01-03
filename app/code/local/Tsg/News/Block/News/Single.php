<?php

class Tsg_News_Block_News_Single
    extends Mage_Core_Block_Template
{
    /**
     * Tsg_News_Block_News_Single constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $collection = $this->getSingleNews();
        $this->setCollection($collection);
    }

    /**
     * This function return single News
     *
     * @return Tsg_News_Model_News||null
     */
    public function getSingleNews()
    {
        $singleNews = null;
        $newsId = $this->getRequest()->getParam('news');
        if (is_numeric($newsId)) {
            /** @var Tsg_News_Model_News $singleNews */
            $singleNews = Mage::getModel('tsg_news/news')->load($newsId);
        }
        return $singleNews;
    }


}