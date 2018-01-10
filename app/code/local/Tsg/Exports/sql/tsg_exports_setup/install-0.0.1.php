<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('tsg_exports/exports'))
    ->addColumn('export_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary' => true,
    ), 'ID')
    ->addColumn('export_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Export Name')
    ->addColumn('file_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'File Name')
    ->addColumn('enable', Varien_Db_Ddl_Table::TYPE_INTEGER, 1, array(
        'nullable' => false,
    ), 'Export Status')
    ->addColumn('format', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
    ), 'Format of export')
    ->addColumn('categories', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable' => false,
    ), 'Category ids')
    ->addColumn('qty_filter', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(),
        'Minimun qty in stock');
$installer->getConnection()->createTable($table);

$installer->endSetup();