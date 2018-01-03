<?php

class Tsg_News_Model_Export
    extends Mage_ImportExport_Model_Export_Entity_Product
{
    /**
     * Prepare data for export.
     *
     * @return void
     */
    protected function _prepareExport()
    {
        //Execution time may be very long
        set_time_limit(0);

        /** @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
        $validAttrCodes = $this->_getExportAttrCodes();
        $writer = $this->getWriter();
        $defaultStoreId = Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID;

        $memoryLimit = trim(ini_get('memory_limit'));
        $lastMemoryLimitLetter = strtolower($memoryLimit[strlen($memoryLimit) - 1]);
        switch ($lastMemoryLimitLetter) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
                break;
            default:
                // minimum memory required by Magento
                $memoryLimit = 250000000;
        }

        // Tested one product to have up to such size
        $memoryPerProduct = 100000;
        // Decrease memory limit to have supply
        $memoryUsagePercent = 0.8;
        // Minimum Products limit
        $minProductsLimit = 500;

        $limitProducts = intval(($memoryLimit * $memoryUsagePercent - memory_get_usage(true)) / $memoryPerProduct);
        if ($limitProducts < $minProductsLimit) {
            $limitProducts = $minProductsLimit;
        }
        $offsetProducts = 0;

        while (true) {
            ++$offsetProducts;

            $dataRows = array();
            $rowCategories = array();
            $rowWebsites = array();
            $rowTierPrices = array();
            $rowGroupPrices = array();
            $rowMultiselects = array();
            $mediaGalery = array();

            // prepare multi-store values and system columns values
            foreach ($this->_storeIdToCode as $storeId => &$storeCode) { // go through all stores
                $collection = $this->_prepareEntityCollection(Mage::getResourceModel('catalog/product_collection'));
                $collection
                    ->setStoreId($storeId)
                    ->setPage($offsetProducts, $limitProducts);
                if ($collection->getCurPage() < $offsetProducts) {
                    break;
                }
                $collection->load();

                if ($collection->count() == 0) {
                    break;
                }

                if ($defaultStoreId == $storeId) {
                    $collection->addCategoryIds()->addWebsiteNamesToResult();

                    // tier and group price data getting only once
                    $rowTierPrices = $this->_prepareTierPrices($collection->getAllIds());
                    $rowGroupPrices = $this->_prepareGroupPrices($collection->getAllIds());

                    // getting media gallery data
                    $mediaGalery = $this->_prepareMediaGallery($collection->getAllIds());
                }
                foreach ($collection as $itemId => $item) { // go through all products
                    $rowIsEmpty = true; // row is empty by default

                    foreach ($validAttrCodes as &$attrCode) { // go through all valid attribute codes
                        $attrValue = $item->getData($attrCode);

                        if (!empty($this->_attributeValues[$attrCode])) {
                            if ($this->_attributeTypes[$attrCode] == 'multiselect') {
                                $attrValue = explode(',', $attrValue);
                                $attrValue = array_intersect_key(
                                    $this->_attributeValues[$attrCode],
                                    array_flip($attrValue)
                                );

                                switch ($this->_attributeScopes[$attrCode]) {
                                    case Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE:
                                        if (isset($rowMultiselects[$itemId][0][$attrCode])
                                            && $attrValue == $rowMultiselects[$itemId][0][$attrCode]
                                        ) {
                                            $attrValue = null;
                                        }
                                        break;

                                    case Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL:
                                        if ($storeId != $defaultStoreId) {
                                            $attrValue = null;
                                        }
                                        break;

                                    case Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE:
                                        $websiteId = $this->_storeIdToWebsiteId[$storeId];
                                        $websiteStoreId = array_search($websiteId, $this->_storeIdToWebsiteId);
                                        if ((isset($rowMultiselects[$itemId][$websiteStoreId][$attrCode])
                                                && $attrValue == $rowMultiselects[$itemId][$websiteStoreId][$attrCode])
                                            || $attrValue == $rowMultiselects[$itemId][0][$attrCode]
                                        ) {
                                            $attrValue = null;
                                        }
                                        break;

                                    default:
                                        break;
                                }

                                if ($attrValue) {
                                    $rowMultiselects[$itemId][$storeId][$attrCode] = $attrValue;
                                    $rowIsEmpty = false;
                                }
                            } else {
                                if (isset($this->_attributeValues[$attrCode][$attrValue])) {
                                    $attrValue = $this->_attributeValues[$attrCode][$attrValue];
                                } else {
                                    $attrValue = null;
                                }
                            }
                        }
                        // do not save value same as default or not existent
                        if ($storeId != $defaultStoreId
                            && isset($dataRows[$itemId][$defaultStoreId][$attrCode])
                            && $dataRows[$itemId][$defaultStoreId][$attrCode] == $attrValue
                        ) {
                            $attrValue = null;
                        }
                        if (is_scalar($attrValue)) {
                            $dataRows[$itemId][$storeId][$attrCode] = $attrValue;
                            $rowIsEmpty = false; // mark row as not empty
                        }
                    }
                    if ($rowIsEmpty) { // remove empty rows
                        unset($dataRows[$itemId][$storeId]);
                    } else {
                        $attrSetId = $item->getAttributeSetId();
                        $dataRows[$itemId][$storeId][self::COL_STORE] = $storeCode;
                        $dataRows[$itemId][$storeId][self::COL_ATTR_SET] = $this->_attrSetIdToName[$attrSetId];
                        $dataRows[$itemId][$storeId][self::COL_TYPE] = $item->getTypeId();

                        if ($defaultStoreId == $storeId) {
                            $rowWebsites[$itemId] = $item->getWebsites();
                            $rowCategories[$itemId] = $item->getCategoryIds();
                        }
                    }
                    if (in_array('mainnews', $validAttrCodes))
                    {
                        $dataRows[$itemId][$storeId]['main_image'] = $item->getMainImage()?:'';
                        $dataRows[$itemId][$storeId]['main_title'] = $item->getMainTitle()?:'';
                        $dataRows[$itemId][$storeId]['main_content'] = $item->getMainContent()?:'';
                    }
                    $item = null;
                }
                $collection->clear();
            }

            if ($collection->getCurPage() < $offsetProducts) {
                break;
            }

            // remove unused categories
            $allCategoriesIds = array_merge(array_keys($this->_categories), array_keys($this->_rootCategories));
            foreach ($rowCategories as &$categories) {
                $categories = array_intersect($categories, $allCategoriesIds);
            }

            // prepare catalog inventory information
            $productIds = array_keys($dataRows);
            $stockItemRows = $this->_prepareCatalogInventory($productIds);

            // prepare links information
            $linksRows = $this->_prepareLinks($productIds);
            $linkIdColPrefix = array(
                Mage_Catalog_Model_Product_Link::LINK_TYPE_RELATED => '_links_related_',
                Mage_Catalog_Model_Product_Link::LINK_TYPE_UPSELL => '_links_upsell_',
                Mage_Catalog_Model_Product_Link::LINK_TYPE_CROSSSELL => '_links_crosssell_',
                Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED => '_associated_'
            );
            $configurableProductsCollection = Mage::getResourceModel('catalog/product_collection');
            $configurableProductsCollection->addAttributeToFilter(
                'entity_id',
                array(
                    'in' => $productIds
                )
            )->addAttributeToFilter(
                'type_id',
                array(
                    'eq' => Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE
                )
            );
            $configurableData = array();
            while ($product = $configurableProductsCollection->fetchItem()) {
                $productAttributesOptions = $product->getTypeInstance(true)->getConfigurableOptions($product);

                foreach ($productAttributesOptions as $productAttributeOption) {
                    $configurableData[$product->getId()] = array();
                    foreach ($productAttributeOption as $optionValues) {
                        $priceType = $optionValues['pricing_is_percent'] ? '%' : '';
                        $configurableData[$product->getId()][] = array(
                            '_super_products_sku' => $optionValues['sku'],
                            '_super_attribute_code' => $optionValues['attribute_code'],
                            '_super_attribute_option' => $optionValues['option_title'],
                            '_super_attribute_price_corr' => $optionValues['pricing_value'] . $priceType
                        );
                    }
                }
            }

            // prepare custom options information
            $customOptionsData = array();
            $customOptionsDataPre = array();
            $customOptCols = array(
                '_custom_option_store',
                '_custom_option_type',
                '_custom_option_title',
                '_custom_option_is_required',
                '_custom_option_price',
                '_custom_option_sku',
                '_custom_option_max_characters',
                '_custom_option_sort_order',
                '_custom_option_row_title',
                '_custom_option_row_price',
                '_custom_option_row_sku',
                '_custom_option_row_sort'
            );

            foreach ($this->_storeIdToCode as $storeId => &$storeCode) {
                $skip = false;
                $options = Mage::getResourceModel('catalog/product_option_collection')
                    ->reset()
                    ->addTitleToResult($storeId)
                    ->addPriceToResult($storeId)
                    ->addProductToFilter($productIds)
                    ->addValuesToResult($storeId);

                foreach ($options as $option) {
                    $row = array();
                    $productId = $option['product_id'];
                    $optionId = $option['option_id'];
                    $customOptions = isset($customOptionsDataPre[$productId][$optionId])
                        ? $customOptionsDataPre[$productId][$optionId]
                        : array();

                    if ($defaultStoreId == $storeId) {
                        $row['_custom_option_type'] = $option['type'];
                        $row['_custom_option_title'] = $option['title'];
                        $row['_custom_option_is_required'] = $option['is_require'];
                        $row['_custom_option_price'] = $option['price']
                            . ($option['price_type'] == 'percent' ? '%' : '');
                        $row['_custom_option_sku'] = $option['sku'];
                        $row['_custom_option_max_characters'] = $option['max_characters'];
                        $row['_custom_option_sort_order'] = $option['sort_order'];

                        // remember default title for later comparisons
                        $defaultTitles[$option['option_id']] = $option['title'];
                    } elseif ($option['title'] != $customOptions[0]['_custom_option_title']) {
                        $row['_custom_option_title'] = $option['title'];
                    }
                    $values = $option->getValues();
                    if ($values) {
                        $firstValue = reset($values);
                        $priceType = $firstValue['price_type'] == 'percent' ? '%' : '';

                        if ($defaultStoreId == $storeId) {
                            $row['_custom_option_row_title'] = $firstValue['title'];
                            $row['_custom_option_row_price'] = $firstValue['price'] . $priceType;
                            $row['_custom_option_row_sku'] = $firstValue['sku'];
                            $row['_custom_option_row_sort'] = $firstValue['sort_order'];

                            $defaultValueTitles[$firstValue['option_type_id']] = $firstValue['title'];
                        } elseif ($firstValue['title'] != $customOptions[0]['_custom_option_row_title']) {
                            $row['_custom_option_row_title'] = $firstValue['title'];
                        }
                    }
                    if ($row) {
                        if ($defaultStoreId != $storeId) {
                            $row['_custom_option_store'] = $this->_storeIdToCode[$storeId];
                        }
                        $customOptionsDataPre[$productId][$optionId][] = $row;
                        $skip = true;
                    }
                    foreach ($values as $value) {
                        if ($skip) {
                            $skip = false;
                            continue;
                        }

                        $row = array();
                        $valuePriceType = $value['price_type'] == 'percent' ? '%' : '';

                        if ($defaultStoreId == $storeId) {
                            $row['_custom_option_row_title'] = $value['title'];
                            $row['_custom_option_row_price'] = $value['price'] . $valuePriceType;
                            $row['_custom_option_row_sku'] = $value['sku'];
                            $row['_custom_option_row_sort'] = $value['sort_order'];
                        } else {
                            $row['_custom_option_row_title'] = $value['title'];
                        }
                        if ($row) {
                            if ($defaultStoreId != $storeId) {
                                $row['_custom_option_store'] = $this->_storeIdToCode[$storeId];
                            }
                            $customOptionsDataPre[$option['product_id']][$option['option_id']][] = $row;
                        }
                    }
                    $option = null;
                }
                $options = null;
            }
            foreach ($customOptionsDataPre as $productId => &$optionsData) {
                $customOptionsData[$productId] = array();

                foreach ($optionsData as $optionId => &$optionRows) {
                    $customOptionsData[$productId] = array_merge($customOptionsData[$productId], $optionRows);
                }
                unset($optionRows, $optionsData);
            }
            unset($customOptionsDataPre);

            if ($offsetProducts == 1) {
                // create export file
                $headerCols = array_merge(
                    array(
                        self::COL_SKU,
                        self::COL_STORE,
                        self::COL_ATTR_SET,
                        self::COL_TYPE,
                        self::COL_CATEGORY,
                        self::COL_ROOT_CATEGORY,
                        '_product_websites'
                    ),
                    $validAttrCodes,
                    reset($stockItemRows) ? array_keys(end($stockItemRows)) : array(),
                    array(),
                    array(
                        '_links_related_sku',
                        '_links_related_position',
                        '_links_crosssell_sku',
                        '_links_crosssell_position',
                        '_links_upsell_sku',
                        '_links_upsell_position',
                        '_associated_sku',
                        '_associated_default_qty',
                        '_associated_position'
                    ),
                    array('_tier_price_website', '_tier_price_customer_group', '_tier_price_qty', '_tier_price_price'),
                    array('_group_price_website', '_group_price_customer_group', '_group_price_price'),
                    array(
                        '_media_attribute_id',
                        '_media_image',
                        '_media_lable',
                        '_media_position',
                        '_media_is_disabled'
                    )
                );
                if (in_array('mainnews', $validAttrCodes)) {
                    $mainNewsOptions = array(
                        '0' => 'main_image',
                        '1' => 'main_title',
                        '2' => 'main_content',
                    );
                    $headerCols = array_merge($headerCols, $mainNewsOptions);
                }
                // have we merge custom options columns
                if ($customOptionsData) {
                    $headerCols = array_merge($headerCols, $customOptCols);
                }

                // have we merge configurable products data
                if ($configurableData) {
                    $headerCols = array_merge($headerCols, array(
                        '_super_products_sku',
                        '_super_attribute_code',
                        '_super_attribute_option',
                        '_super_attribute_price_corr'
                    ));
                }

                $writer->setHeaderCols($headerCols);
            }

            foreach ($dataRows as $productId => &$productData) {
                foreach ($productData as $storeId => &$dataRow) {
                    if ($defaultStoreId != $storeId) {
                        $dataRow[self::COL_SKU] = null;
                        $dataRow[self::COL_ATTR_SET] = null;
                        $dataRow[self::COL_TYPE] = null;
                    } else {
                        $dataRow[self::COL_STORE] = null;
                        $dataRow += $stockItemRows[$productId];
                    }

                    $this->_updateDataWithCategoryColumns($dataRow, $rowCategories, $productId);
                    if ($rowWebsites[$productId]) {
                        $dataRow['_product_websites'] = $this->_websiteIdToCode[array_shift($rowWebsites[$productId])];
                    }
                    if (!empty($rowTierPrices[$productId])) {
                        $dataRow = array_merge($dataRow, array_shift($rowTierPrices[$productId]));
                    }
                    if (!empty($rowGroupPrices[$productId])) {
                        $dataRow = array_merge($dataRow, array_shift($rowGroupPrices[$productId]));
                    }
                    if (!empty($mediaGalery[$productId])) {
                        $dataRow = array_merge($dataRow, array_shift($mediaGalery[$productId]));
                    }
                    foreach ($linkIdColPrefix as $linkId => &$colPrefix) {
                        if (!empty($linksRows[$productId][$linkId])) {
                            $linkData = array_shift($linksRows[$productId][$linkId]);
                            $dataRow[$colPrefix . 'position'] = $linkData['position'];
                            $dataRow[$colPrefix . 'sku'] = $linkData['sku'];

                            if (null !== $linkData['default_qty']) {
                                $dataRow[$colPrefix . 'default_qty'] = $linkData['default_qty'];
                            }
                        }
                    }
                    if (!empty($customOptionsData[$productId])) {
                        $dataRow = array_merge($dataRow, array_shift($customOptionsData[$productId]));
                    }
                    if (!empty($configurableData[$productId])) {
                        $dataRow = array_merge($dataRow, array_shift($configurableData[$productId]));
                    }
                    if (!empty($rowMultiselects[$productId][$storeId])) {
                        foreach ($rowMultiselects[$productId][$storeId] as $attrKey => $attrVal) {
                            if (isset($rowMultiselects[$productId][$storeId][$attrKey])) {
                                $dataRow[$attrKey] = array_shift($rowMultiselects[$productId][$storeId][$attrKey]);
                            }
                        }
                    }

                    $writer->writeRow($dataRow);
                    // calculate largest links block
                    $largestLinks = 0;

                    if (isset($linksRows[$productId])) {
                        $linksRowsKeys = array_keys($linksRows[$productId]);
                        foreach ($linksRowsKeys as $linksRowsKey) {
                            $largestLinks = max($largestLinks, count($linksRows[$productId][$linksRowsKey]));
                        }
                    }
                    $additionalRowsCount = max(
                        count($rowCategories[$productId]),
                        count($rowWebsites[$productId]),
                        $largestLinks
                    );
                    if (!empty($rowTierPrices[$productId])) {
                        $additionalRowsCount = max($additionalRowsCount, count($rowTierPrices[$productId]));
                    }
                    if (!empty($rowGroupPrices[$productId])) {
                        $additionalRowsCount = max($additionalRowsCount, count($rowGroupPrices[$productId]));
                    }
                    if (!empty($mediaGalery[$productId])) {
                        $additionalRowsCount = max($additionalRowsCount, count($mediaGalery[$productId]));
                    }
                    if (!empty($customOptionsData[$productId])) {
                        $additionalRowsCount = max($additionalRowsCount, count($customOptionsData[$productId]));
                    }
                    if (!empty($configurableData[$productId])) {
                        $additionalRowsCount = max($additionalRowsCount, count($configurableData[$productId]));
                    }
                    if (!empty($rowMultiselects[$productId][$storeId])) {
                        foreach ($rowMultiselects[$productId][$storeId] as $attributes) {
                            $additionalRowsCount = max($additionalRowsCount, count($attributes));
                        }
                    }
                    if ($additionalRowsCount) {
                        for ($i = 0; $i < $additionalRowsCount; $i++) {
                            $dataRow = array();

                            $this->_updateDataWithCategoryColumns($dataRow, $rowCategories, $productId);
                            if ($rowWebsites[$productId]) {
                                $dataRow['_product_websites'] = $this
                                    ->_websiteIdToCode[array_shift($rowWebsites[$productId])];
                            }
                            if (!empty($rowTierPrices[$productId])) {
                                $dataRow = array_merge($dataRow, array_shift($rowTierPrices[$productId]));
                            }
                            if (!empty($rowGroupPrices[$productId])) {
                                $dataRow = array_merge($dataRow, array_shift($rowGroupPrices[$productId]));
                            }
                            if (!empty($mediaGalery[$productId])) {
                                $dataRow = array_merge($dataRow, array_shift($mediaGalery[$productId]));
                            }
                            foreach ($linkIdColPrefix as $linkId => &$colPrefix) {
                                if (!empty($linksRows[$productId][$linkId])) {
                                    $linkData = array_shift($linksRows[$productId][$linkId]);
                                    $dataRow[$colPrefix . 'position'] = $linkData['position'];
                                    $dataRow[$colPrefix . 'sku'] = $linkData['sku'];

                                    if (null !== $linkData['default_qty']) {
                                        $dataRow[$colPrefix . 'default_qty'] = $linkData['default_qty'];
                                    }
                                }
                            }
                            if (!empty($customOptionsData[$productId])) {
                                $dataRow = array_merge($dataRow, array_shift($customOptionsData[$productId]));
                            }
                            if (!empty($configurableData[$productId])) {
                                $dataRow = array_merge($dataRow, array_shift($configurableData[$productId]));
                            }
                            if (!empty($rowMultiselects[$productId][$storeId])) {
                                foreach ($rowMultiselects[$productId][$storeId] as $attrKey => $attrVal) {
                                    if (isset($rowMultiselects[$productId][$storeId][$attrKey])) {
                                        $dataRow[$attrKey] = array_shift($rowMultiselects[$productId][$storeId][$attrKey]);
                                    }
                                }
                            }
                            $writer->writeRow($dataRow);
                        }
                    }
                }
            }
        }
        return $writer->getContents();
    }

    protected function _prepareEntityCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        if (!isset($this->_parameters[Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP])
            || !is_array($this->_parameters[Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP])) {
            $exportFilter = array();
        } else {
            $exportFilter = $this->_parameters[Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP];
        }
        $exportAttrCodes = $this->_getExportAttrCodes();

        foreach ($this->filterAttributeCollection($this->getAttributeCollection()) as $attribute) {
            $attrCode = $attribute->getAttributeCode();

            // filter applying
            if (isset($exportFilter[$attrCode])) {
                $attrFilterType = Mage_ImportExport_Model_Export::getAttributeFilterType($attribute);

                if (Mage_ImportExport_Model_Export::FILTER_TYPE_SELECT == $attrFilterType) {
                    if (is_scalar($exportFilter[$attrCode]) && trim($exportFilter[$attrCode])) {
                        $collection->addAttributeToFilter($attrCode, array('eq' => $exportFilter[$attrCode]));
                    }
                } elseif (Mage_ImportExport_Model_Export::FILTER_TYPE_INPUT == $attrFilterType) {
                    if (is_scalar($exportFilter[$attrCode]) && trim($exportFilter[$attrCode])) {
                        $collection->addAttributeToFilter($attrCode, array('like' => "%{$exportFilter[$attrCode]}%"));
                    }
                } elseif (Mage_ImportExport_Model_Export::FILTER_TYPE_DATE == $attrFilterType) {
                    if (is_array($exportFilter[$attrCode]) && count($exportFilter[$attrCode]) == 2) {
                        $from = array_shift($exportFilter[$attrCode]);
                        $to = array_shift($exportFilter[$attrCode]);

                        if (is_scalar($from) && !empty($from)) {
                            $date = Mage::app()->getLocale()->date($from, null, null, false)->toString('MM/dd/YYYY');
                            $collection->addAttributeToFilter($attrCode, array('from' => $date, 'date' => true));
                        }
                        if (is_scalar($to) && !empty($to)) {
                            $date = Mage::app()->getLocale()->date($to, null, null, false)->toString('MM/dd/YYYY');
                            $collection->addAttributeToFilter($attrCode, array('to' => $date, 'date' => true));
                        }
                    }
                } elseif (Mage_ImportExport_Model_Export::FILTER_TYPE_NUMBER == $attrFilterType) {
                    if (is_array($exportFilter[$attrCode]) && count($exportFilter[$attrCode]) == 2) {
                        $from = array_shift($exportFilter[$attrCode]);
                        $to = array_shift($exportFilter[$attrCode]);

                        if (is_numeric($from)) {
                            $collection->addAttributeToFilter($attrCode, array('from' => $from));
                        }
                        if (is_numeric($to)) {
                            $collection->addAttributeToFilter($attrCode, array('to' => $to));
                        }
                    }
                }
            }
            if (in_array($attrCode, $exportAttrCodes)) {
                $collection->addAttributeToSelect($attrCode);

            }

            if (in_array('mainnews', $exportAttrCodes)) {
                $tableName = Mage::getSingleton('core/resource')->getTableName('tsg_news/list');
                $collection->joinAttribute(
                    'news_at_id',
                    'catalog_product/mainnews',
                    'entity_id',
                    null, 'left'
                )->joinTable(
                    array('data' => $tableName),
                    'news_id = news_at_id',
                    array(
                        'main_image' => 'news_image',
                        'main_content' => 'news_content',
                        'main_title' => 'news_title',
                    ),
                    null,
                    'left'
                );
            }

            return $collection;
        }

    }
}