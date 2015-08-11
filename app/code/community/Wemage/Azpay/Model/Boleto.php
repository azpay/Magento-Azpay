<?php

class Wemage_Azpay_Model_Boleto extends Wemage_Azpay_Model_Api {

    protected $_code = 'azpay_boleto';
    protected $_formBlockType = 'azpay/form_boleto';
    protected $_infoBlockType = 'azpay/info_boleto';
    protected $_isGateway = true;
    protected $_canUseForMultishipping = false;
    protected $_isInitializeNeeded = true;

    public function initialize($paymentAction, $stateObject) {
        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();
        $this->_place($payment, $order->getBaseTotalDue());
        return $this;
    }

    public function _place(Mage_Sales_Model_Order_Payment $payment, $amount) {

        try {
            $order = $payment->getOrder();
            $azpay = new AZPay($this->_merchantId, $this->_merchantKey);
            $azpay->curl_timeout = 60;
            $azpay->config_order['reference'] = $order->getRealOrderId();
            $azpay->config_order['totalAmount'] = Mage::helper('azpay')->formatAmount($amount);
            $azpay->config_options['urlReturn'] = Mage::getUrl('azpay/transaction_boleto/postback');
            $azpay->config_boleto['acquirer'] = $this->getConfigData('operator');
            $azpay->config_boleto['expire'] = $this->_generateExpirationDate();
            $azpay->config_boleto['nrDocument'] = substr($order->getRealOrderId(), 1);
            $azpay->config_boleto['amount'] = Mage::helper('azpay')->formatAmount($amount);
            $azpay->config_boleto['instructions'] = $this->getConfigData('instructions');
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
            // Log
            if ($this->getConfigData('log')) { Mage::log($azpay, null, "azpay_boleto.log");            }
            // Execute
            $azpay->boleto()->execute();


        } catch (AZPay_Error $e) {
            Mage::log($e->getMessage(), null, "azpay_boleto_error.log");
            $error = $azpay->responseError();
            return Mage::throwException("Ocorreu um problema com o seu pedido. Tente novamente ou entre em contato conosco informando este código: " . $error['status_message']);
        }
        // Response
        $gateway_response = $azpay->response();

        // azpay info
        $payment->setAzpayTransactionId($gateway_response->transactionId)
                ->setAzpayBoletoUrl($gateway_response->processor->Boleto->details->urlBoleto);

        return $this;
    }

    protected function _generateExpirationDate() {
        $days = $this->getConfigData('days_to_expire');
        $result = Mage::getModel('core/date')->date('Y-m-d', strtotime("+ $days days"));
        return $result;
    }

}
