<?php
// adding new columns for tsg_news_list table
$installer = $this;
$installer->startSetup();
$newsTable = $installer->getTable('tsg_news/list');
$installer->getConnection()->addColumn($newsTable, 'created_at', array(
    'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
    'nullable' => true,
    'default' => null,
    'comment' => 'Created At',
));
$installer->getConnection()->addColumn($newsTable, 'news_priority', array(
    'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'nullable' => true,
    'default' => null,
    'comment' => 'News Priority',
));

$installer->endSetup();