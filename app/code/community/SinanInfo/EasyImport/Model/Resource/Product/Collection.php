<?php

class SinanInfo_EasyImport_Model_Resource_Product_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _afterLoad()
    {
        $that = parent::_afterLoad();
        $that = $that->getSelect()->join(
            array('as' => 'importscript_urls'),
            $this->getMainTable() . '.url_id = as.id',
            array()
        );
        var_dump($that->__toString());
        //var_dump($that->getSelect());
        return $that;
    }
}
