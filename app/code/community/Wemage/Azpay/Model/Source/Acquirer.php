<?php

class Wemage_Azpay_Model_Source_Acquirer
{

    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label' => Mage::helper('azpay')->__('CIELO - BUY PAGE LOJA')),
            //array('value' => '2', 'label' => Mage::helper('azpay')->__('CIELO - BUY PAGE CIELO')),
            array('value' => '3', 'label' => Mage::helper('azpay')->__('REDE - KOMERCI WEBSERVICE')),
            //array('value' => '4', 'label' => Mage::helper('azpay')->__('REDE - KOMERCI INTEGRADO')),
            array('value' => '6', 'label' => Mage::helper('azpay')->__('ELAVON')),
            array('value' => '22', 'label' => Mage::helper('azpay')->__('GETNET')),
            array('value' => '20', 'label' => Mage::helper('azpay')->__('STONE'))
        );
    }

}
