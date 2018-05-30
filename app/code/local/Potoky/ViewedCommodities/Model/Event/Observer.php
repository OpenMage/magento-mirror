<?php

class Potoky_ViewedCommodities_Model_Event_Observer extends Mage_Reports_Model_Event_Observer
{
    /**
     * Customer login action. Stores this fact in session.
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Reports_Model_Event_Observer
     */
    public function customerLogin(Varien_Event_Observer $observer)
    {
        $this->reactToLog('reset');

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
        $this->reactToLog('clear');

        return parent::customerLogout($observer);
    }

    /**
     * Unsets session variable that stores the time ща expiration of the JS Block
     * and creates a cookie that should make the JS Block clear its data or reloaded
     * it from the server
     *
     * @param string $type
     */
    private function reactToLog($type)
    {
        if (isset($_SESSION['viewed_commodities'])) {
            unset($_SESSION['viewed_commodities']);
        }
        setcookie('viewed_commodities', $type, 0, '/');

    }

}