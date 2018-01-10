<?php

class Tsg_Exports_Block_Adminhtml_Exports_Edit_Tab_General
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('tsg_exports')->__('Основное');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('tsg_exports')->__('Основное');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Preparing general tab
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('tsg_exports');
        $model = Mage::registry('tsg_exports');

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => $helper->__('Основная информация'),
        ));

        if ($model->getId()) {
            $fieldset->addField('export_id', 'hidden', array(
                'name' => 'export_id',
            ));
        }

        $fieldset->addField('export_name', 'text', array(
            'name' => 'export_name',
            'label' => $helper->__('Имя Экспорта'),
            'required' => true,
        ));
        $fieldset->addField('file_name', 'text', array(
            'name' => 'file_name',
            'label' => $helper->__('Имя Файла'),
            'required' => true,
        ));
        $fieldset->addField('enable', 'select', array(
            'name' => 'enable',
            'values' => array('0' => 'Disable', '1' => 'Enable',),
            'label' => $helper->__('Статус'),
            'required' => true,
        ));
        $fieldset->addField('format', 'select', array(
            'name' => 'format',
            'label' => $helper->__('Формат'),
            'values' => array('yaml' => 'YAML', 'json' => 'JSON',),
            'title' => $helper->__('Format'),
            'required' => true,
        ));
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}