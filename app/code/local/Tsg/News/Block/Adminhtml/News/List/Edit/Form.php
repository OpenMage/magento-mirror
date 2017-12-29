<?php

class Tsg_News_Block_Adminhtml_News_List_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    public function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            )
        );
        $helper = Mage::helper('tsg_news');
        $fieldset = $form->addFieldset('news_form', array('legend' => $helper->__('News information')));

        $fieldset->addField('news_title', 'text', array(
            'label' => $helper->__('Title'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'news_title',
        ));

        $fieldset->addField('news_content', 'text', array(
            'label' => $helper->__('Content'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'news_content',
        ));

        $fieldset->addField('news_image', 'image', array(
            'label' => $helper->__('Image'),
            'class' => 'required-entry',
            'required' => false,
            'name' => 'news_image',
        ));
        $fieldset->addField('news_id', 'hidden', array(
            'label' => $helper->__('News Id'),
            'required' => false,
            'name' => 'news_id',
        ));
        // Setting news data if news_id is not null
        $news_id = $this->getRequest()->getParam('news_id');
        if($news_id) {
            $model = Mage::getModel('tsg_news/news')->load($news_id);
            $form->setValues($model->getData());
        }

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}