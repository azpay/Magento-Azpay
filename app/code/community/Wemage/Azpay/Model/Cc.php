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
        $info->setAdditionalInformation('customer_ip', $data->getCustomerip());
        #Mage::log($info, null, "debug.log");
        return $this;
    }

    public function initialize($paymentAction, $stateObject) {
        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();
        $this->_place($payment, $order->getBaseTotalDue(), $paymentAction);
        return $this;
    }

    public function _place(Varien_Object $payment, $amount, $requestType) {

        try {

            $order = $payment->getOrder();

            $billingAddress = $order->getBillingAddress();
            $flag = $payment->getCcType();
            $parcels = $payment->getInstallments();
            $parcelMax = Mage::getStoreConfig('payment/azpay_cc/max_installments_'.$flag.'');
            $parcelMinValue = ceil(Mage::getStoreConfig('payment/azpay_cc/min_installment_value_'.$flag.''));
            $amountTotal = ceil(Mage::helper('azpay')->formatAmount($amount));
            $parcelValue = ceil($amountTotal / $parcels);

      			// Check quantity of parcels
      			if ($parcels > $parcelMax)
              return Mage::throwException("Quantidade inválida de parcelas.");

            // Check value of parcel
      			if ($parcelValue < $parcelMinValue)
              return Mage::throwException("Valor da parcela inválido.");

            //AZPay config
            $azpay = new AZPay($this->_merchantId, $this->_merchantKey);
            $azpay->curl_timeout = 60;
            $azpay->config_order['reference'] = $order->getRealOrderId();
            $azpay->config_order['totalAmount'] = Mage::helper('azpay')->formatAmount($amount);
            $azpay->config_options['urlReturn'] = Mage::getUrl('azpay/transaction_cc/postback');
            $azpay->config_card_payments['amount'] = Mage::helper('azpay')->formatAmount($amount);
            $azpay->config_card_payments['acquirer'] = $this->getConfigData('acquirer_'.$flag.'');
            $azpay->config_card_payments['method'] = ($parcels == '1') ? 1 : 2;
            $azpay->config_card_payments['flag'] = $payment->getCcType();
            $azpay->config_card_payments['numberOfPayments'] = $parcels;
            $azpay->config_card_payments['cardHolder'] = $payment->getCcOwner();
            $azpay->config_card_payments['cardNumber'] = Mage::helper('core')->decrypt($payment->getCcNumber());
            $azpay->config_card_payments['cardSecurityCode'] = Mage::helper('core')->decrypt($payment->getCcCid());
            $azpay->config_card_payments['cardExpirationDate'] = $payment->getCcExpYear() . $payment->getCcExpMonth();

            if ($order->getCustomerId()) {
              $azpay->config_billing['customerIdentity'] = $order->getCustomerId();
            } else {
              $azpay->config_billing['customerIdentity'] = $order->getRealOrderId();
            }

            $azpay->config_billing['name'] = $order->getCustomerName();
            $azpay->config_billing['address'] = $billingAddress->getStreet(1);
            $azpay->config_billing['addressNumber'] = $billingAddress->getStreet(2);
            $azpay->config_billing['address2'] = $billingAddress->getStreet(3) ? $billingAddress->getStreet(3) : '';
            $azpay->config_billing['city'] = $billingAddress->getCity();
            $azpay->config_billing['state'] = $billingAddress->getRegionCode();
            $azpay->config_billing['postalCode'] = Zend_Filter::filterStatic($billingAddress->getPostcode(), 'Digits');
            $azpay->config_billing['phone'] = $billingAddress->getTelephone();
            $azpay->config_billing['email'] = $order->getCustomerEmail();

            //Authorize method
            if ($requestType == "authorize") {

                //Fraud config
                $phoneData = Mage::helper('azpay')->splitTelephone($billingAddress->getTelephone());
                $costumerIP = $payment->getAdditionalInformation('customer_ip');
                $azpay->config_options['fraud'] = "true";
                $azpay->config_options['costumerIP'] = $costumerIP;
                $azpay->config_billing['phonePrefix'] = $phoneData['ddd'];
                $azpay->config_billing['phoneNumber'] = $phoneData['number'];

                foreach ($order->getItemsCollection() as $_item) {
                    $azpay->config_product['productName'] = $_item->getProduct()->getName();
                    $azpay->config_product['quantity'] = $_item->getQtyOrdered();
                    $azpay->config_product['price'] = $_item->getProduct()->getFinalPrice($_item->getQtyOrdered());
                }

                //Prepare authorization to transaction
                $operation = $azpay->authorize();
                //XML to save in log
                $azpay->getXml();

            }

            //Sale method
            if ($requestType == "authorize_capture") {
                //Prepare authorization to direct sale
                $operation = $azpay->sale();
            }

            //Log
            if ($this->getConfigData('log')) {
                Mage::log($azpay, null, "azpay_cc.log");
            }

            //Execute operation
            $operation->execute();

        } catch (AZPay_Error $e) {

          # HTTP 409 - AZPay Error
          $error = $azpay->responseError();
          $response_message = $error['error_message'];
          Mage::log($e->getMessage(), null, "azpay_cc_error.log");
          return Mage::throwException("Ocorreu um erro com o seu pagamento: " . $response_message);

        } catch (AZPay_Curl_Exception $e) {

          # Connection Error
          $response_message = $e->getMessage();
          return Mage::throwException("Ocorreu um erro com o seu pagamento: " . $response_message);

        } catch (AZPay_Exception $e) {

          # General Error
          $response_message = $e->getMessage();
          return Mage::throwException("Ocorreu um erro com o seu pagamento: " . $response_message);

        }

        //Response AZPay
        $gateway_response = $azpay->response();

        //Check response return
        if ( !isset($gateway_response) ) {
            return Mage::throwException("Ocorreu um erro com a resposta do pagamento");
        }

        $response_status = intval($gateway_response->status);
        $response_message = Config::$STATUS_MESSAGES[$response_status]['title'];

        if ( $response_status == Config::$STATUS['AUTHORIZED'] || $response_status == Config::$STATUS['APPROVED'] ) {

        } else {
            return Mage::throwException("Ocorreu um erro com o seu pagamento: " . $response_message);
        }

        // azpay info
        $payment->setAzpayTransactionId($gateway_response->transactionId);

        return $this;
    }

}
