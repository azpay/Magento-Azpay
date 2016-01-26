<?php

class Wemage_Azpay_Model_Cc extends Wemage_Azpay_Model_Api {

    protected $_code = 'azpay_cc';
    protected $_formBlockType = 'azpay/form_cc';
    protected $_infoBlockType = 'azpay/info_cc';
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canRefund = true;
    protected $_canUseForMultishipping = false;
    protected $_isInitializeNeeded = true;

    const REQUEST_TYPE_AUTH_CAPTURE = 'AUTH_CAPTURE';
    const REQUEST_TYPE_AUTH_ONLY = 'AUTH_ONLY';
    const REQUEST_TYPE_CAPTURE_ONLY = 'CAPTURE_ONLY';

    public function assignData($data) {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();
        $ccNumberSize = strlen($data->getCcNumber());
        $ccLast4 = substr($data->getCcNumber(), $ccNumberSize - 4, 4);
        $info->setCcType($data->getCcType())
                ->setCcNumber(Mage::helper('core')->encrypt($data->getCcNumber()))
                ->setCcOwner($data->getCcOwner())
                ->setCcExpMonth($data->getCcExpMonth())
                ->setCcExpYear($data->getCcExpYear())
                ->setCcLast4(Mage::helper('core')->encrypt($ccLast4))
                ->setCcCid(Mage::helper('core')->encrypt($data->getCcCid()));
        $info->setInstallments($data->getInstallments());
        return $this;
    }

    public function initialize($paymentAction, $stateObject) {
        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();
        $this->_place($payment, $order->getBaseTotalDue(),$paymentAction);
        return $this;
    }


    public function _place(Varien_Object $payment, $amount, $requestType) {

        try {

            $order = $payment->getOrder();
            $azpay = new AZPay($this->_merchantId, $this->_merchantKey);
            $azpay->curl_timeout = 60;
            $azpay->config_order['reference'] = $order->getRealOrderId();
            $azpay->config_order['totalAmount'] = Mage::helper('azpay')->formatAmount($amount);
            $azpay->config_card_payments['amount'] = Mage::helper('azpay')->formatAmount($amount);
            $parcels = $payment->getInstallments();
            $azpay->config_card_payments['acquirer'] = $this->getConfigData('acquirer');
            $azpay->config_card_payments['method'] = ($parcels == '1') ? 1 : 2;
            $azpay->config_card_payments['flag'] = $payment->getCcType();
            $azpay->config_card_payments['numberOfPayments'] = $parcels;
            $azpay->config_card_payments['cardHolder'] = $payment->getCcOwner();
            $azpay->config_card_payments['cardNumber'] = Mage::helper('core')->decrypt($payment->getCcNumber());
            $azpay->config_card_payments['cardSecurityCode'] = Mage::helper('core')->decrypt($payment->getCcCid());
            $azpay->config_card_payments['cardExpirationDate'] = $payment->getCcExpYear() . $payment->getCcExpMonth();
            $billingAddress = $order->getBillingAddress();
            $azpay->config_billing['customerIdentity'] = $order->getCustomerTaxvat();
            $azpay->config_billing['name'] = $order->getCustomerName();
            $azpay->config_billing['address'] = $billingAddress->getStreet(1) . ',' . $billingAddress->getStreet(2);
            $azpay->config_billing['address2'] = $billingAddress->getStreet(3) ? $billingAddress->getStreet(3) : '';
            $azpay->config_billing['city'] = $billingAddress->getCity();
            $azpay->config_billing['state'] = $billingAddress->getRegionCode();
            $azpay->config_billing['postalCode'] = Zend_Filter::filterStatic($billingAddress->getPostcode(), 'Digits');
            $azpay->config_billing['phone'] = Mage::helper('azpay')->splitTelephone($billingAddress->getTelephone());
            $azpay->config_billing['email'] = $order->getCustomerEmail();


            if ($this->getConfigData('log')) {
                Mage::log($azpay, null, "azpay_cc.log");
            }            

            if ($requestType == "authorize") {
                // Execute Authorization - Prepare authorization to transaction
                $azpay->authorize()->execute();
            } elseif ($requestType == "authorize_capture") {
                // Execute Sale - Prepare authorization and capture (direct sale), to transaction
                $azpay->sale()->execute();
            }
        } catch (AZPay_Error $e) {
            Mage::log($e->getMessage(), null, "azpay_cc_error.log");
            $error = $azpay->responseError();
            return Mage::throwException("Ocorreu um problema com o seu pedido. Tente novamente ou entre em contato conosco informando este código: " . $error['status_message']);
        }

        // Response
        $gateway_response = $azpay->response();


        if ($gateway_response->status != Config::$STATUS['APPROVED']) {
            //      return Mage::throwException('Pagamento não Autorizado');
        }

        // azpay info
        $payment->setAzpayTransactionId($gateway_response->transactionId);

        return $this;
    }

}
