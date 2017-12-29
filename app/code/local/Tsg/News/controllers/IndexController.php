<?php

class Tsg_News_IndexController
    extends Mage_Core_Controller_Front_Action
{
    public function newsListAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function readAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}