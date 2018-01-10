<?php

class Tsg_Exports_Block_Adminhtml_Exports_Edit_Tab_Filters
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
        return Mage::helper('tsg_exports')->__('Фильтры');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('tsg_exports')->__('Фильтры');
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
     * Preparing filter tab
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        /** @var Mage_Eav_Model_Config $attributeModel */
        $attributeModel = Mage::getModel('eav/config');
        // Getting attributes
        $sharesAttribute = $attributeModel->getAttribute('catalog_product', 'tsg_shares');
        $markdownAttribute = $attributeModel->getAttribute('catalog_product', 'tsg_markdown');
        $providerAttribute = $attributeModel->getAttribute('catalog_product', 'tsg_provider');
        // Shares Options
        $sharesAttributeOptions = $sharesAttribute->getSource()->getAllOptions(true, true);
        $sharesValues = array();
        foreach ($sharesAttributeOptions as $option) {
            $sharesValues[] = ['value' => $option['value'],'label' => $option['label']];
        }
        // Markdown Options
        $markdownAttributeOptions = $markdownAttribute->getSource()->getAllOptions(true, true);
        $markdownValues = array();
        foreach ($markdownAttributeOptions as $option) {
            $markdownValues[] = ['value' => $option['value'],'label' => $option['label']];
        }
        // Provider Options
        $providerAttributeOptions = $providerAttribute->getSource()->getAllOptions(true, true);
        $providerValues = array();
        foreach ($providerAttributeOptions as $option) {
            $providerValues[] = ['value' => $option['value'],'label' => $option['label']];
        }
        $helper = Mage::helper('tsg_exports');
        $model = Mage::registry('tsg_exports');
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => $helper->__('Фильтры'),
        ));
        $fieldset->addField('qty_filter', 'text', array(
            'name' => 'qty_filter',
            'label' => $helper->__('Фильтр по количеству'),
            'required' => false,
        ));
        $fieldset->addField('shares_filter', 'multiselect', array(
            'name' => 'shares_filter',
            'label' => $helper->__('Фильтр по акциям'),
            'required' => false,
            'values' => array(
                '1' => array(
                    'value' => $sharesValues,
                    'label' => 'Акции',
                ),
            ),
        ));
        $fieldset->addField('markdown_filter', 'multiselect', array(
            'name' => 'markdown_filter',
            'label' => $helper->__('Фильтр по уценке'),
            'required' => false,
            'values' => array(
                '1' => array(
                    'value' => $markdownValues,
                    'label' => 'Уценка',
                ),
            ),
        ));
        $fieldset->addField('provider_filter', 'multiselect', array(
            'name' => 'provider_filter',
            'label' => $helper->__('Фильтр по поставщику'),
            'required' => false,
            'values' => array(
                '1' => array(
                    'value' => $providerValues,
                    'label' => 'Поставщик',
                ),
            ),
        ));
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}