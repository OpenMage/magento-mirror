<?php

class Potoky_ViewedProducts_Model_Lifetime extends Mage_Core_Model_Config_Data
{
    /**
     * Validates data entered to the module"s System Configuration field
     * and saves the configuration in case of success.
     *
     * @return Mage_Core_Model_Abstract
     * @throws Mage_Core_Exception
     */
    public function save()
    {
        $lifetime = $this->getValue();
        $sessCookLifetime = (int) Mage::getStoreConfig(Mage_Core_Model_Cookie::XML_PATH_COOKIE_LIFETIME);
        if (filter_var($lifetime, FILTER_VALIDATE_INT, array(
            "options" => array(
                "min_range" => 120,
                "max_range" => 7776000
            ))) === false) {

            Mage::throwException('The value of the field should be an integer in range from 120 to 7776000.');
        }
        elseif ($lifetime > $sessCookLifetime) {
            Mage::getSingleton('adminhtml/session')
                ->addNotice(
                    'Please mind that the  lifetime of the JS block will not last longer then the session cookie lifetime
                     which for now is configured to ' . $sessCookLifetime . ' sec. It is also strictly dependant on other sessional lifetimes.'
                );
        }
        return parent::save();
    }
}