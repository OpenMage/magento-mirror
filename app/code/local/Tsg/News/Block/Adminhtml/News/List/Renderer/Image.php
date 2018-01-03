<?php

class Tsg_News_Block_Adminhtml_News_List_Renderer_Image
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * This function is rendering images for news list grid
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        if ($value !== null) {
            $imagePath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . DS . $value;
            $imageLink = '<img src = "' . $imagePath . '" width="50" height="50" />';
            return $imageLink;
        }
    }
}

?>