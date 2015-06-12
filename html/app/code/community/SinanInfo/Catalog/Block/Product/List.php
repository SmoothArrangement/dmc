<?php
// app/code/community/SinanInfo/Catalog/Block/Product/List.php
/**
 * Product list
 *
 * @category   SinanInfo
 * @package    SinanInfo_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class SinanInfo_Catalog_Block_Product_List extends Mage_Catalog_Block_Product_List
{
    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        $session = Mage::getModel('core/session');
        $bike = $session->getData('bike');
        $collection = parent::_getProductCollection();
        if (isset($bike)) {
            $collection->addAttributeToFilter('model', array('eq' => $bike['model']))
                ->addAttributeToFilter('manufacturer', array('eq' => $bike['manufacturer']))
                ->addAttributeToFilter('buildyear', array('eq' => $bike['buildyear']));
        }
        // add universal
        
        $universal = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToFilter('manufacturer', array('eq' => 'universal'))
            ->addAttributeToFilter('model', array('eq' => 'universal'))
            ->addAttributeToFilter('buildyear', array('eq' => 'universal'));

        $merged_ids = array_merge($collection->getAllIds(), $universal->getAllIds());
        
        $category = Mage::registry('current_category');
        $merged_collection = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addCategoryFilter($category)
            ->addAttributeToFilter('entity_id', array('in' => $merged_ids))
            ->addAttributeToSelect('*');

        return $merged_collection;
    }
}
