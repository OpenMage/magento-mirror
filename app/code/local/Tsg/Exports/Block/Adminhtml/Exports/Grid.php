<?php

class Tsg_Exports_Block_Adminhtml_Exports_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Tsg_Exports_Block_Adminhtml_Exports_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setDefaultSort('export_id');
        $this->setId('tsg_exports_exports_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);

    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Returning collection Model of exports
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'tsg_exports/exports_collection';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('export_id',
            array(
                'header' => $this->__('Export ID'),
                'align' => 'right',
                'index' => 'export_id',
            ));
        $this->addColumn('export_name',
            array(
                'header' => $this->__('Export Name'),
                'align' => 'right',
                'index' => 'export_name',
            ));
        $this->addColumn('file_name',
            array(
                'header' => $this->__('File Name'),
                'align' => 'right',
                'index' => 'file_name',
            ));
        $this->addColumn('enable',
            array(
                'header' => $this->__('Status'),
                'align' => 'right',
                'index' => 'enable',
            ));
        $this->addColumn('format',
            array(
                'header' => $this->__('Format'),
                'align' => 'right',
                'index' => 'format',
            ));
        $this->addColumn('categories',
            array(
                'header' => $this->__('Categories'),
                'align' => 'right',
                'index' => 'categories',
            ));
        $this->addColumn('qty_filter',
            array(
                'header' => $this->__('QTY filter'),
                'align' => 'right',
                'index' => 'qty_filter',
            ));
        $this->addColumn('shares_filter',
            array(
                'header' => $this->__('Shares filter'),
                'align' => 'right',
                'index' => 'shares_filter',
            ));
        $this->addColumn('markdown_filter',
            array(
                'header' => $this->__('Markdown filter'),
                'align' => 'right',
                'index' => 'markdown_filter',
            ));
        $this->addColumn('provider_filter',
            array(
                'header' => $this->__('Provider filter'),
                'align' => 'right',
                'index' => 'provider_filter',
            ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getExportId()));
    }


    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('mass_action');
        $this->getMassactionBlock()->addItem('generate', array(
            'label'    => Mage::helper('tsg_exports')->__('Generate'),
            'url'      => $this->getUrl('*/*/massGenerate'),
            'confirm'  => Mage::helper('tsg_exports')->__('Are you sure?')
        ));

        return $this;
    }

}