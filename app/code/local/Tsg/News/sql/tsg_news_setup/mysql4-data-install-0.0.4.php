<?php
// creating news
$news = array();
for ($i = 1; $i < 6; $i++) {
    $news[] = array(
        'news_title' => 'Title ' . $i,
        'news_content' => 'Conetnt ' . $i,
        'news_priority' => $i,
        'created_at' => strftime('%Y-%m-%d %H:%M:%S', time()),
    );
}

foreach ($news as $item) {
    $newsModel = Mage::getModel('tsg_news/news');
    $newsModel->setData($item)->save();
}

// creating category
$categoryName = 'Custom Products';
$category = Mage::getResourceModel('catalog/category_collection')
    ->addFieldToFilter('name', $categoryName);
if ($category->getFirstItem()->getId() === null) {
    Mage::register('isSecureArea', 1);
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
    $category = Mage::getModel('catalog/category');
    $category->setPath('1/2')// set parent to be root category
    ->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)
        ->setName($categoryName)
        ->setIsActive(true)
        ->save();
}
// creating products
$productCat = Mage::getResourceModel('catalog/category_collection')
    ->addFieldToFilter('name', $categoryName)
    ->getFirstItem();
$categoryId = $productCat->getId();

$newsCollection = Mage::getModel('tsg_news/news')->getCollection()->addFieldToSelect('news_id');
$newsIds = array();
foreach ($newsCollection as $news) {
    $newsIds[] = $news->getId();
}
$newsIds = array_flip($newsIds);

$productsData = array();
for ($i = 1; $i < 6; $i++) {
    $productsData[$i] = array(
        'website_ids' => array(Mage::app()->getStore(true)->getWebsite()->getId()),
        'attribute_set_id' => 4,
        'type_id' => 'simple',
        'created_at' => strtotime('now'),
        'sku' => 'product ' . $i,
        'name' => 'Product' . $i,
        'weight' => 4,
        'status' => 1,
        'tax_class_id' => 4,
        'visibility' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
        'price' => 100,
        'description' => 'This is product ' . $i,
        'short_description' => 'This is product ' . $i,
        'stock_data' => array(
            'use_config_manage_stock' => 0,
            'manage_stock' => 1,
            'min_sale_qty' => 1,
            'max_sale_qty' => 2,
            'is_in_stock' => 1,
            'qty' => 999
        ),
        'category_ids' => $categoryId,
    );
    $newsData = array();

    if ($i < 5) {
        $newsData['newslist'] = array_rand($newsIds, mt_rand(2, count($newsIds)));
        $mainKey = array_rand($newsData['newslist'], 1);
        $newsData['mainnews'] = $newsData['newslist'][$mainKey];
        $productsData[$i] = array_merge($productsData[$i], $newsData);
    }
}

foreach ($productsData as $data) {
    $product = Mage::getModel('catalog/product');
    $product->addData($data);
    $product->save();
}

$this->endSetup();