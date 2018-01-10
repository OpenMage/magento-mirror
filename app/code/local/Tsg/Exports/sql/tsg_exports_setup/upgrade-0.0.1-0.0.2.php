<?php


/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'tsg_shares', [
    'type' => 'int',
    'input' => 'select',
    'label' => 'Акция',
    'sort_order' => 1000,
    'is_visible' => 1,
    'required' => 0,
    'searchable' => 0,
    'filterable' => 0,
    'unique' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'backend' => 'eav/entity_attribute_backend_array',
    'option' => [
        'values' => [
            0 => 'Не задано',
            1 => 'Да',
            2 => 'Нет',
        ]
    ],

]);
$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'tsg_markdown', [
    'type' => 'int',
    'input' => 'select',
    'label' => 'Уценка',
    'sort_order' => 1000,
    'is_visible' => 1,
    'required' => 0,
    'searchable' => 0,
    'filterable' => 0,
    'unique' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'backend' => 'eav/entity_attribute_backend_array',
    'option' => [
        'values' => [
            0 => 'Не задано',
            1 => 'Да',
            2 => 'Нет',
        ]
    ],

]);
$setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'tsg_provider', [
    'type' => 'int',
    'input' => 'select',
    'label' => 'Поставщик',
    'sort_order' => 1000,
    'is_visible' => 1,
    'required' => 0,
    'searchable' => 0,
    'filterable' => 0,
    'unique' => 0,
    'comparable' => 0,
    'visible_on_front' => 0,
    'user_defined' => 1,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'backend' => 'eav/entity_attribute_backend_array',
    'option' => [
        'values' => [
            0 => 'Поставщик 1',
            1 => 'Поставщик 2',
        ]
    ],

]);

$installer->endSetup();