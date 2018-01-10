<?php

class Tsg_Exports_Model_Exports
    extends Mage_Core_Model_Abstract
{

    protected $_categoryInstance = null;

    protected $_attributesToSelect = array(
        '0' => 'url_key',
        '1' => 'price',
        '2' => 'image',
    );

    protected $_attributeKeysToJsonExport = array(
        'url_key' => '1',
        'price' => '2',
        'image' => '3',
        'sku' => '4',
        'category_id' => '5',
        'qty' => '6',
        'currency_id' => '7',
    );


    protected function _construct()
    {
        $this->_init('tsg_exports/exports');
    }

    /**
     * @param $export
     */
    public function generateExport($export)
    {
        $productCollection = array();
        if (!empty($export['categories'])) {
            /** @var  Mage_Catalog_Model_Resource_Product_Collection $productCollection */
            $productCollection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToSelect($this->_attributesToSelect)
                ->joinField(
                    'category_id', 'catalog/category_product', 'category_id',
                    'product_id = entity_id', null, 'left'
                )->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', null,
                    'left'
                )->addAttributeToFilter('category_id', array('in' => explode(',', $export['categories'])));
            if (!empty($export['shares_filter'])) {
                $productCollection->addAttributeToFilter('tsg_shares',
                    array('in' => explode(',', $export['shares_filter'])));
            }
            if (!empty($export['markdown_filter'])) {
                $productCollection->addAttributeToFilter('tsg_markdown',
                    array('in' => explode(',', $export['markdown_filter'])));;
            }
            if (!empty($export['provider_filter'])) {
                $productCollection->addAttributeToFilter('tsg_provider',
                    array('in' => explode(',', $export['provider_filter'])));;
            }
            if ($export['qty_filter'] !== null) {
                $productCollection->addAttributeToFilter('qty',
                    array('gteq' => $export['qty_filter']));;
            }
            $productCollection->getSelect()->group('e.entity_id');
        }

        switch ($export['format']) {
            case 'yaml' :
                $content = $this->exportYml($productCollection);
                $exoportFileName = $export['file_name'] . '.yml';
                break;
            case 'json' :
                $content = $this->exportJson($productCollection);
                $exoportFileName = $export['file_name'] . '.json';
                break;
            default :
                $content = 'Export format is invalid';
                $exoportFileName = 'default';
        }
        file_put_contents(Mage::getBaseDir('media') . DS . $exoportFileName, $content);
    }

    /**
     * Export products in yml format
     *
     * @param $productCollection
     * @return string
     */
    public function exportYml($productCollection)
    {
        $currencyСode = $this->getDefaultCurrencyCode();
        /** @var XMLWriter xml */
        $this->xml = new XMLWriter();
        $this->xml->openMemory();
        $this->xml->setIndentString('  ');
        $this->xml->startDocument('1.0', 'windows-1251');
        $this->xml->writeDTD('yml_catalog', null, 'shops.dtd');
        $this->xml->text("\n");
        $this->xml->setIndent(true);
        $this->xml->startElement('yml_catalog');
        $this->xml->writeAttribute('date', date('Y-m-d H:i:s'));
        $this->xml->startElement('offers');
        foreach ($productCollection as $product) {
            $this->xml->startElement('offer');
            $this->xml->writeAttribute('id', $product->getEntityId());
            $this->xml->writeAttribute('type', 'vendor.model');
            $this->xml->writeAttribute('available', 'true');
            $this->xml->writeElement('url', $product->getUrlKey());
            $this->xml->writeElement('price', $product->getPrice());
            $this->xml->writeElement('currencyId',$currencyСode);
            $this->xml->writeElement('categoryId', $product->getCategoryId());
            $productImageContent = 'no_selection';
            if ($product->getImage() !== 'no_selection') {
                $productImageContent = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product'
                    . $product->getImage();
            }
            $this->xml->writeElement('picture', $productImageContent);
            $this->xml->writeElement('qty', $product->getQty());
            $this->xml->endElement();
        }
        $this->xml->endElement();
        $this->xml->endElement();
        $this->xml->endDocument();
        return $this->xml->outputMemory();
    }

    /**
     * Retuning json Product content
     *
     * @param $productCollection
     * @return string
     */
    public function exportJson($productCollection)
    {
        $content = '';
        $productsData = array();
        $currencyCode = $this->getDefaultCurrencyCode();
        foreach ($productCollection as $product) {
            if ($product->getImage() !== 'no_selection') {
                $product['image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product'
                    . $product->getImage();
            }
            $product->setCurrencyId($currencyCode);
            $productsData['offers']['offer ' . $product->getEntityId()] = array_intersect_key($product->getData(),
                $this->_attributeKeysToJsonExport);
        }
        $content .= json_encode($productsData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        return $content;
    }

    /**
     * @return mixed
     */
    public function getSelectedCategories()
    {
        if (!$this->hasSelectedCategories()) {
            $categories = array();
            foreach ($this->getSelectedCategoriesArray() as $category) {
                $categories[] = $category;
            }
            $this->setSelectedCategories($categories);
        }
        return $this->getData('selected_categories');
    }

    /**
     * @return array
     */
    public function getSelectedCategoriesArray()
    {
        $categoryArray = explode(',', $this->getCategories());
        if (!is_array($categoryArray)) {
            $categoryArray[] = $categoryArray;
        }
        return $categoryArray;
    }

    /**
     * @return string
     */
    public function getDefaultCurrencyCode()
    {
        $defaultStoreId = Mage::app()
            ->getWebsite(true)
            ->getDefaultGroup()
            ->getDefaultStoreId();
        return Mage::app()->getStore($defaultStoreId)->getCurrentCurrencyCode();

    }


}