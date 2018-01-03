<?php
// applying tsg package
$installer = $this;
$installer->startSetup();
$configUpdate = new Mage_Core_Model_Config();
$configUpdate->saveConfig('design/package/name', 'tsg', 'default', 0);
$configUpdate->saveConfig('design/theme/template', 'default', 'default', 0);
$configUpdate->saveConfig('design/theme/skin', 'default', 'default', 0);
$configUpdate->saveConfig('design/theme/layout', 'default', 'default', 0);
$installer->endSetup();



