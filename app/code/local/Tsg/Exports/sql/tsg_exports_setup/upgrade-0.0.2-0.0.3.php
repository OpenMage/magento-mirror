<?php

$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('tsg_exports/exports'),
        'shares_filter',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'default' => null,
            'comment' => 'Shares filter'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('tsg_exports/exports'),
        'markdown_filter',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'default' => null,
            'comment' => 'Markdown filter'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('tsg_exports/exports'),
        'provider_filter',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'default' => null,
            'comment' => 'Provider filter'
        )
    );

$installer->endSetup();