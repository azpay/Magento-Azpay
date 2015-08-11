<?php

class Wemage_Azpay_Block_Checkout_Success_Payment_Default extends Mage_Core_Block_Template {

    public function setPayment(Varien_Object $payment) {
        $this->setData('payment', $payment);
        return $this;
    }

    public function getPayment() {
        return $this->_getData('payment');
    }

    public function getOrder() {
        return $this->getPayment()->getOrder();
    }

}
