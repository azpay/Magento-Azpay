<?php

class Wemage_Azpay_Model_Source_Cctypes {

    public function toOptionArray() {
          return array(
          array('value' => 'visa', 'label' => Mage::helper('azpay')->__('Visa'), 'mask' => '9999 9999 9999 9999'),
          array('value' => 'mastercard', 'label' => Mage::helper('azpay')->__('MasterCard'), 'mask' => '9999 9999 9999 9999'),
          array('value' => 'diners', 'label' => Mage::helper('azpay')->__('Diners'), 'mask' => '9999 9999 9999 99'),
          array('value' => 'elo', 'label' => Mage::helper('azpay')->__('Elo'), 'mask' => '9999 9999 9999 9999'),
          array('value' => 'amex', 'label' => Mage::helper('azpay')->__('Amex'), 'mask' => '9999 9999 9999 999'),
          array('value' => 'aura', 'label' => Mage::helper('azpay')->__('Aura'), 'mask' => '9999 9999 9999 9999999'),
          array('value' => 'discover', 'label' => Mage::helper('azpay')->__('Discover'), 'mask' => '9999 9999 9999 9999'),
          array('value' => 'jcb', 'label' => Mage::helper('azpay')->__('JCB'), 'mask' => '9999 9999 9999 9999'),
          );
    }

}
