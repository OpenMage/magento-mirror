<?php

class Tsg_News_Model_Observer
{

    public function updateNews($observer)
    {
        $product = $observer->getProduct();
        $newslist = $product->getNewslist();
        $mainNews = $product->getMainnews();

        if ($newslist[0] === 'no') {
            $mainNews = 'no';
        } elseif ((is_array($newslist) && $newslist[0] !== 'no') && (empty($mainNews) || $mainNews === 'no')) {
            $mainNews = Mage::getModel('tsg_news/news')->getCollection()
                ->addIdsFilter($newslist)
                ->setOrder('created_at', 'asc')
                ->getFirstItem()
                ->getId();
        } elseif ((is_array($newslist) && $newslist[0] !== 'no') && $mainNews !== 'no') {
            $productId = $product->getId();
            if ($productId) {
                $currentProductNewsData = explode(',', Mage::getModel('catalog/product')->load($productId)
                    ->getNewslist());
                if ($currentProductNewsData['0'] !== 'no') {
                    if ($currentProductNewsData != $newslist) {
                        $mainNews = $this->mostPriorityNews($newslist);
                    }
                }
            }
        }

        $product->setNewslist($newslist);
        $product->setMainnews($mainNews);
        $observer->setProduct($product);

    }

    /**
     * Calculating most priority News and return its id
     *
     * @param $newsIds
     * @return string
     */
    public function mostPriorityNews($newsIds)
    {
        $news = Mage::getModel('tsg_news/news')->getCollection()->addIdsFilter($newsIds);
        $rating = array();
        foreach ($news as $item) {
            $rating[$item->getNewsId()] = strtotime($item->getCreatedAt()) + ($item->getNewsPriority() * 86400);
        }
        $priorityNews = array_keys($rating, max($rating));
        return $priorityNews['0'];
    }
}