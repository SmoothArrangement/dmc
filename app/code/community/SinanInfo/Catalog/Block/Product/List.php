<?php
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
        $limit = $this->getRequest()->getParam('limit');
        $page = $this->getRequest()->getParam('p');
        
        $limit = isset($limit) ? $limit : 12;
        $page = isset($page) ? $page : 1;

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

        $merged_ids = array_unique(array_merge($collection->getAllIds(), $universal->getAllIds()));
        $pagination = array_chunk($merged_ids, $limit);

        $ids = array();
        if (!empty($pagination)) {
            $ids = $pagination[$page - 1];
        }
        

        $category = Mage::registry('current_category');
        $merged_collection = Mage::getResourceModel('catalog/product_collection')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addCategoryFilter($category)
            ->addAttributeToFilter('entity_id', array('in' => $ids))
            ->addAttributeToSelect('*');

        return $merged_collection;
    }
}
