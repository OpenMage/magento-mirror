<?php

// adding select attribute to products

$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute(
    Mage_Catalog_Model_Product::ENTITY, "mainnews",
    array(
        "type" => "text",
        "backend" => "eav/entity_attribute_backend_array",
        'group' => 'News',
        "label" => "Select Main News",
        "input" => "select",
        "source" => "tsg_news/news",
        'is_visible' => 1,
        'required' => 0,
        'searchable' => 0,
        'filterable' => 0,
        'unique' => 0,
        'comparable' => 0,
        'visible_on_front' => 0,
        'user_defined' => 1,
    )
);
$installer->endSetup();
?>