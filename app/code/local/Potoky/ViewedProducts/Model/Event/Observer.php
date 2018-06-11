<?php

class Potoky_ViewedProducts_Model_Event_Observer extends Mage_Reports_Model_Event_Observer
{
    /**
     * Customer login action. Stores this fact in session.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Reports_Model_Event_Observer
     */
    public function customerLogin(Varien_Event_Observer $observer)
    {
        Mage::helper('viewedproducts/session')->unsetSessionSetCookieForViewedProducts('reset');

        return parent::customerLogin($observer);
    }

    /**
     * Customer logout processing. Stores this fact in session.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Reports_Model_Event_Observer
     */
    public function customerLogout(Varien_Event_Observer $observer)
    {
        Mage::helper('viewedproducts/session')->unsetSessionSetCookieForViewedProducts('clear');

        return parent::customerLogout($observer);
    }
}