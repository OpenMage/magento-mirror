<?php

class Tsg_News_Model_News
    extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('tsg_news/news');
    }

    /**
     * This function is used in product attributes to get options to select
     * @return array
     */
    public function getAllOptions()
    {
        $news[] = [
            'value' => 'no',
            'label' => 'There is no news',
        ];
        foreach ($this->getCollection() as $index => $value) {
            $news[] = [
                'value' => $index,
                'label' => $value->getNewsTitle(),
            ];
        }
        return $news;
    }

}
