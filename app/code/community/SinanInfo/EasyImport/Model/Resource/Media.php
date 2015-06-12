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
class SinanInfo_EasyImport_Model_Resource_Media extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Init main table and id field name
     */
    protected function _construct()
    {
        $this->_init('easyimport/importscript_media', 'id');
    }
}
