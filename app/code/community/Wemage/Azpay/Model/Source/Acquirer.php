<?php

class Wemage_Azpay_Model_Source_Acquirer
{

    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => Mage::helper('azpay')->__('Flag Disabled')),
            array('value' => '1', 'label' => Mage::helper('azpay')->__('CIELO - BUY PAGE LOJA')),
            array('value' => '3', 'label' => Mage::helper('azpay')->__('REDE - KOMERCI WEBSERVICE')),
            array('value' => '6', 'label' => Mage::helper('azpay')->__('ELAVON')),
            array('value' => '22', 'label' => Mage::helper('azpay')->__('GETNET')),
            array('value' => '20', 'label' => Mage::helper('azpay')->__('STONE')),
            array('value' => '24', 'label' => Mage::helper('azpay')->__('GLOBAL PAYMENTS')),
            array('value' => '25', 'label' => Mage::helper('azpay')->__('BIN')),
            array('value' => '22', 'label' => Mage::helper('azpay')->__('GETNET'))
        );
    }

}
