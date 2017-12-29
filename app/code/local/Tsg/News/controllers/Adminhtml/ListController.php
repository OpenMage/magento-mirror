<?php

class Tsg_News_Adminhtml_ListController
    extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('News'))
            ->_title($this->__('List'));
        $this->loadLayout();
        $this->renderLayout();

    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * This method is saving and updating the news
     */
    public function saveAction()
    {
        $model = Mage::getModel('tsg_news/news');
        if ($data = $this->getRequest()->getPost()) {
            if (isset($_FILES['news_image']['name']) && !empty($_FILES['news_image']['name'])) {
                try {
                    $uploader = new Varien_File_Uploader('news_image');
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $newsMediaDir = Mage::getBaseDir('media') . DS . 'news';
                    if (!file_exists($newsMediaDir)) {
                        mkdir($newsMediaDir, 0777, true);
                    }
                    $uploader->save($newsMediaDir, $_FILES['news_image']['name']);
                    $data['news_image'] = 'news' . DS . $_FILES['news_image']['name'];
                } catch (Exception $e) {

                }
            } elseif (isset($data['news_image']['delete'])) {
                $data['news_image'] = "";
            } elseif (!empty($data['news_image']['value'])) {
                $data['news_image'] = $data['news_image']['value'];
            }
            $newsId = $this->getRequest()->getParam('news_id') ?: null;
            $model->setData($data)->setId($newsId);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('tsg_news')->__('News was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
    }

}