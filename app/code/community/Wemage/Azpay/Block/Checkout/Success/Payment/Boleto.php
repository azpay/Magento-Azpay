<?php

class Wemage_Azpay_Block_Checkout_Success_Payment_Boleto extends Wemage_Azpay_Block_Checkout_Success_Payment_Default {

    public function getBoletoUrl() {
        return $this->getPayment()->getAzpayBoletoUrl();
    }

}
