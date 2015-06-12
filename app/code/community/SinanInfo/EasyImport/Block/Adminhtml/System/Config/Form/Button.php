<?php
/**
 * PHP version 5
 *
 * @category  SinanInfo
 * @package   SinanInfo_Import
 * @author    SinanInfo Team <team@sinaninfo.com>
 * @copyright 2015 SinanInfo Team (http://www.sinaninfo.com)
 * @version   1.0.0
 * @since     1.0.0
 */

class SinanInfo_EasyImport_Block_Adminhtml_System_Config_Form_Button extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sinaninfo/system/config/button.phtml');
    }
 
    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }
 
    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getAjaxImportUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_easyimport/import');
    }
    
    public function getAjaxCountImportUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_easyimport/count');
    }

    public function getAjaxCategoryImportUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/adminhtml_easyimport/importcategory');
    }
        
    public function getProductImportButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'id'        => 'easyimport_product_button',
            'label'     => $this->helper('adminhtml')->__('Import Product'),
            'onclick'   => 'javascript:importProduct(); return false;'
        ));

        return $button->toHtml();
    }

    public function getCategoryImportButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'id'        => 'easyimport_category_button',
            'label'     => $this->helper('adminhtml')->__('Import Category'),
            'onclick'   => 'javascript:importCategory(); return false;'
        ));

        return $button->toHtml();
    }
}
