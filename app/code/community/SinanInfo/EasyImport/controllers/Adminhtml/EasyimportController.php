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

class SinanInfo_EasyImport_Adminhtml_EasyimportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Return some checking result
     *
     * @return void
     */
    public function importAction()
    {
        $limit = Mage::app()->getRequest()->getParam('limit');
        $offset = Mage::app()->getRequest()->getParam('offset');

        $limit = isset($limit) ? $limit : 10;
        $offset = isset($offset) ? $offset : 0;

        $collection = Mage::getModel('easyimport/product')->getCollection();
        $collection
            ->join(
                array('urlTable'=> 'importscript_urls'),
                'main_table.url_id = urlTable.id',
                array()
            )
            ->join(
                array('mediaTable'=> 'importscript_media'),
                'main_table.prod_id = mediaTable.prod_id && mediaTable.media_type = "img"',
                array()
            )
            ->getSelect()
            ->group(array('main_table.id'))
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('main_table.*')
            ->columns('urlTable.*')
            ->columns('mediaTable.*')
            ->columns('GROUP_CONCAT(mediaTable.image) as image')
            ->limit($limit, $offset);

        $data = $collection->getData();
        if ($this->pushData($data)) {
            $count = count($data);
            if ($count == 10) {
                $result = array(
                    'data' => $data,
                    'more' => count($data),
                    'offset' => $offset + 10
                );
            } else {
                $result = array(
                    'data' => $data,
                    'stop' => true
                );
            }
            $result['error'] = false;

        } else {
            $result = array(
                'error' => true,
                'message' => 'have something wrong when push data'
            );
        }

        $result = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($result);
    }

    public function importcategoryAction()
    {
        $collection = Mage::getModel('easyimport/product')->getCollection();
        $collection
            ->join(
                array('urlTable'=> 'importscript_urls'),
                'main_table.url_id = urlTable.id',
                array()
            )
            ->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('main_table.*')
            ->columns('urlTable.*');

        $data = $collection->getData();
        
        if ($this->pushCategoryData($data)) {
            $count = count($data);
            $result = array(
                'data' => $data,
                'count' => $count,
                'error' => false
            );
        } else {
            $result = array(
                'error' => true,
                'message' => 'have something wrong when push data'
            );
        }

        $result = json_encode($result);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($result);
    }

    public function countAction()
    {
        $collection = Mage::getModel('easyimport/product')->getCollection();
        $collection
            ->join(
                array('urlTable'=> 'importscript_urls'),
                'main_table.url_id = urlTable.id',
                array()
            )
            ->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('count(*)');

        $data = $collection->getData();
        
        $result = json_encode($data);
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody($result);
    }

    public function pushData($source)
    {
        $product = array();
        $category = array();

        foreach ($source as $item) {
            $prod = array(
                '_attribute_set' => 'Components-Set',
                '_type' => 'simple',
                '_product_websites' => 'base',
                'delivery_time' => '2-3 Werktage',
                'visibility' => '4',
                'weight' => '0',
                'qty' => '10',
                'is_in_stock' => '1',
                'meta_autogenerate' => 'yes',
            );
            $cat = array(
                '_root' => 'Components',
                'is_active' => 'yes',
                'include_in_menu' => 'yes',
                'meta_description' => 'Meta Test',
                'available_sort_by' => 'position',
                'default_sort_by' => 'position'
            );
            #sku
            $prod['sku'] = $this->uniqueSku($source, $item['artnr'], $item['url_id']);
            
            $provider = '';
            if ($item['category'] == 'au') {
                switch ($item['provider']) {
                    case 'ak':
                        $provider = "Akrapovic";
                        $c1 = "Motorrad/Auspuffanlagen/Akrapovic";
                        break;
                    case 'te':
                        $provider = "Termignoni";
                        $c1 = "Motorrad/Auspuffanlagen/Termignoni";
                        break;
                    case 'aa':
                        $provider = "Akrapovic";
                        $c1 = "Auto/Auspuffanlagen/Akrapovic";
                        break;
                    default:
                        $provider = " ";
                        break;
                }
            }
            if (isset($c1)) {
                $c2 = explode('/', $c1);
                $curr = '';
                foreach ($c2 as $key => $value) {
                    if ($key == 0) {
                        $curr .= $value;
                    } else {
                        $curr .= '/' . $value;
                    }
                    $cat['_category'] = $curr;
                    $cat['description'] = $value;
                    $category[] = $cat;
                }
            }

            # name = "provider" "h2" (without img and h2 tag text only e.g. Slip-ON-Linie 08-15 "brand" "model" "year"
            $n1 = strip_tags($item['h2']);
            $prod['name'] = $provider . ' ' . $n1 . ' ' . $item['brand'] . ' ' . str_replace('_', ' ', $item['model']) . ' ' . $item['yearofbuild'];
            
            $prod['short_description'] = $item['h2'];
            
            # description = "h1" "h2" "description"
            $prod['description'] = $item['h1'] . $item['h2'] . $item['description'];
            
            # buildyear | color | manufacturer | price | model
            $prod['buildyear'] = $item['yearofbuild'];
            $prod['color'] = $item['color'];
            $prod['manufacturer'] = $item['brand'];
            $prod['price'] = str_replace(',', '.', $item['price']);
            $prod['model'] = str_replace('_', ' ', $item['model']);
            
            # image
            $oriImage = explode(',', $item['image']);
            $image = array();

            // filter to get image exist
            foreach ($oriImage as $img) {
                if ($this->checkRemoteFile($img)) {
                    $tmp = pathinfo($img);
                    if (isset($tmp['extension'])) {
                        $ext = $tmp['extension'];
                        if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') {
                            $image[] = $img;
                        }
                    }
                }
            }
            if (!empty($image)) {
                $prod['_media_image'] = $image;
                $prod['_media_attribute_id'] = 88;

                $image_label = array();
                $position = array();
                $disable = array();

                foreach ($image as $key => $img) {
                    $img = pathinfo($img);
                    if ($key == 0) {
                        $prod['image'] = $img['basename'];
                        $prod['small_image'] = $img['basename'];
                        $prod['thumbnail'] = $img['basename'];
                    }
                    $image_label[] = $img['filename'];
                    $position[] = $key;
                    $disable[] = 0;
                }
                $prod['_media_lable'] = $image_label;
                $prod['_media_is_disabled'] = 1;
                $prod['_media_position'] = $position;
                $prod['_media_is_disabled'] = $disable;
            }
            
            $product[] = $prod;
        }
        $import = Mage::getModel('fastsimpleimport/import');

        $prod_import = $import;
        $cat_import = $import;
        try {
            $prod_import->setUseNestedArrays(true)->processProductImport($product);
            $cat_import->processCategoryImport($category);
            
        } catch (Exception $e) {
            echo 'CATEGORY IMPORT ERROR: ' . $cat_import->getErrorMessage() . '<br />';
            echo 'PRODUCT IMPORT ERROR: ' . $prod_import->getErrorMessage() . '<br />';
            return false;
        }
        return true;
    }

    public function pushCategoryData($source)
    {
        $product_category = array();

        foreach ($source as $item) {
            $cat_prod = array('_root' => 'Components', 'is_active' => 'yes', 'position' => 1);
            $cat_prod['_sku'] = $this->uniqueSku($source, $item['artnr'], $item['url_id']);
            if ($item['category'] == 'au') {
                switch ($item['provider']) {
                    case 'ak':
                        $provider = "Akrapovic";
                        $c1 = "Motorrad/Auspuffanlagen/Akrapovic";
                        break;
                    case 'te':
                        $provider = "Termignoni";
                        $c1 = "Motorrad/Auspuffanlagen/Termignoni";
                        break;
                    case 'aa':
                        $provider = "Akrapovic";
                        $c1 = "Auto/Auspuffanlagen/Akrapovic";
                        break;
                    default:
                        $provider = " ";
                        break;
                }
            }
            if (isset($c1)) {
                $cat_prod['_category'] = $c1;
                $product_category[] = $cat_prod;
            }
        }

        $cat_prod_import = Mage::getModel('fastsimpleimport/import');

        try {
            $cat_prod_import->processCategoryProductImport($product_category);
        } catch (Exception $e) {
            echo 'CATEGORY_PRODUCT IMPORT ERROR: ' . $cat_prod_import->getErrorMessage() . '<br />';
            return false;
        }
        return true;
    }

    public function uniqueSku($source, $sku, $plus = '')
    {
        foreach ($source as $value) {
            if ($value['artnr'] == $sku) {
                $sku = $sku . '_' . $plus;
                $sku = $this->uniqueSku($source, $sku);
            }
        }
        return $sku;
    }

    public function xssProtect($source)
    {
        return strip_tags($source, '<script></script><head></head><body></body><footer></footer><form></form><html></html>');
    }

    public function checkRemoteFile($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // don't download content
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if (curl_exec($ch) !== false) {
            return true;
        } else {
            return false;
        }
    }
}
