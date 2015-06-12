<?php
// app/code/community/SinanInfo/Vehicles/controllers/IndexController.php
class SinanInfo_Vehicles_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function searchAction()
    {
        // Store selected bike (model | manufacturer | year)
        $request = Mage::app()->getRequest();
        $params = $request->getParams();
        $session = Mage::getModel('core/session');

        if (!empty($params['model']) && !empty($params['manufacturer']) && !empty($params['buildyear'])) {
            $session->setData('bike', $params);
        } else {
            $session->unsetData('bike');
        }

        if (!empty($params['callback'])) {
            $this->_redirectUrl($params['callback']);
        } else {
            $this->_redirect('vehicles');
        }
    }

    public function resetAction()
    {
        $request = Mage::app()->getRequest();
        $params = $request->getParams();
        $session = Mage::getModel('core/session');
        $session->unsetData('bike');

        if (!empty($params['callback'])) {
            $this->_redirectUrl($params['callback']);
        } else {
            $this->_redirect('vehicles');
        }
    }
}
