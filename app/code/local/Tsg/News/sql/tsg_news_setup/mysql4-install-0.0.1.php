<?php
// creating tsg_news_list table
$installer = $this;
$installer->startSetup();
if (!$installer->tableExists('tsg_news/list')) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('tsg_news/list'))
        ->addColumn('news_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'unsigned' => true,
            'nulalble' => false,
            'primary' => true,
        ))
        ->addColumn('news_title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
            'nullable' => false,
        ))
        ->addColumn('news_content', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => false,
        ))
        ->addColumn('news_image', Varien_Db_Ddl_Table::TYPE_VARCHAR, 250, array(
            'nullable' => true,
        ));
    $installer->getConnection()->createTable($table);
}
$installer->endSetup();
