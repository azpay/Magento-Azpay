<?php

class Webmage_Azpay_Model_Source_Cc_Types {

    public function toOptionArray() {
        return array(
            array('value' => 'visa', 'label' => Mage::helper('azpay')->__('Visa')),
            array('value' => 'mastercard', 'label' => Mage::helper('azpay')->__('MasterCard')),
            array('value' => 'dinners', 'label' => Mage::helper('azpay')->__('Dinners')),
            array('value' => 'elo', 'label' => Mage::helper('azpay')->__('Elo')),
            array('value' => 'amex', 'label' => Mage::helper('azpay')->__('Amex')),
            array('value' => 'aura', 'label' => Mage::helper('azpay')->__('Aura')),
            array('value' => 'discover', 'label' => Mage::helper('azpay')->__('Discover')),
            array('value' => 'jcb', 'label' => Mage::helper('azpay')->__('JCB')),
        );
    }

}
