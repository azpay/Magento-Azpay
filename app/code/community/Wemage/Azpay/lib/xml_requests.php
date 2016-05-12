<?php

/**
 * XML Requests Class
 *
 * Generate all the XMLs
 *
 * @author Gabriel Guerreiro <gabrielguerreiro.com>
 * */
class XML_Requests {

    /**
     * Attribute to receive XMLWriter
     * and construct the XMLs
     *
     * @var XMLWriter
     * */
    private $xml_writer;

    function __construct() {

        $this->xml_writer = new XMLWriter();
        $this->xml_writer->openMemory();
        $this->xml_writer->setIndent(true);
    }

    /**
     * Header XML
     *
     * @return void
     */
    private function header() {

        $this->xml_writer->startDocument('1.0', 'UTF-8');
        $this->xml_writer->startElement("transaction-request");
    }

    /**
     * Return XML
     *
     * @return string [XML parsed]
     */
    public function output() {

        $this->xml_writer->endElement();

        return $this->xml_writer->outputMemory();
    }

    /**
     *  Verification XML
     *
     * @param  string $merchantId  [Merchant ID]
     * @param  string $merchantKey [Merchant KEY]
     * @return void
     */
    private function verificationNode($merchantId, $merchantKey) {

        $this->xml_writer->writeElement("version", Config::$AZPAY_VERSION);

        $this->xml_writer->startElement("verification");

        $this->xml_writer->writeElement('merchantId', $merchantId);
        $this->xml_writer->writeElement('merchantKey', $merchantKey);

        $this->xml_writer->endElement();
    }

    /**
     * XML Order
     *
     * @param  array   $order  [Order data]
     * @param  array   $rebill [Rebill data]
     * @return void
     */
    private function orderNode($order, $rebill = null) {

        $this->xml_writer->startElement('order');

        $this->xml_writer->writeElement('reference', $order['reference']);
        $this->xml_writer->writeElement('totalAmount', Utils::formatNumber($order['totalAmount']));

        if (!empty($rebill)) {

            $this->xml_writer->writeElement('period', $rebill['period']);
            $this->xml_writer->writeElement('frequency', $rebill['frequency']);
            $this->xml_writer->writeElement('dateStart', $rebill['dateStart']);
            $this->xml_writer->writeElement('dateEnd', $rebill['dateEnd']);
        }

        $this->xml_writer->endElement();
    }

    /**
     * Billing node
     *
     * @param  array $billing [Billing data]
     * @return void
     */
    private function billingNode($billing) {

        $this->xml_writer->startElement('billing');

        $this->xml_writer->writeElement('customerIdentity', $billing['customerIdentity']);
        $this->xml_writer->writeElement('name', $billing['name']);
        $this->xml_writer->writeElement('address', $billing['address'] . ', ' . $billing['addressNumber']);
        $this->xml_writer->writeElement('address2', $billing['address2']);
        $this->xml_writer->writeElement('city', $billing['city']);
        $this->xml_writer->writeElement('state', $billing['state']);
        $this->xml_writer->writeElement('postalCode', Utils::formatNumber($billing['postalCode']));
        $this->xml_writer->writeElement('country', $billing['country']);
        $this->xml_writer->writeElement('phone', Utils::formatNumber($billing['phone']));
        $this->xml_writer->writeElement('email', $billing['email']);

        $this->xml_writer->endElement();
    }

    /**
     * Authorize XML
     *
     * @param  array $merchant   [Merchant data]
     * @param  array $order      [Order data]
     * @param  array $payments   [Payment data]
     * @param  array $billing    [Billing data]
     * @param  array $options    [Options data]
     * @return void
     */
    public function authorizeXml($merchant, $order, $payments, $billing, $options, $product) {

        $this->header();

        $this->verificationNode($merchant['id'], $merchant['key']);

        $this->xml_writer->startElement('authorize');

        $this->orderNode($order);

        $this->paymentsNode($payments);

        $this->billingNode($billing);

        $this->xml_writer->writeElement('urlReturn', $options['urlReturn']);
        $this->xml_writer->writeElement('fraud', $options['fraud']);
        if ($options['fraud']) {
            $this->fraudXml($billing, $options, $product);
        }
        $this->xml_writer->writeElement('customField', $options['customField']);

        $this->xml_writer->endElement();
    }

    /**
     * Capture XML
     *
     * @param  [String] $merchant_id   [Merchant ID]
     * @param  [String] $merchant_key  [Merchant KEY]
     * @param  [String] $transactionId [AZPay transaction ID]
     * @return void
     */
    public function captureXml($merchant_id, $merchant_key, $transactionId) {

        $this->header();

        $this->verificationNode($merchant_id, $merchant_key);

        $this->xml_writer->startElement('capture');

        $this->xml_writer->writeElement('transactionId', $transactionId);

        $this->xml_writer->endElement();
    }

    /**
     * Sale XML
     *
     * @param  array $merchant [Merchant data]
     * @param  array $order    [Order data]
     * @param  array $payments [Payment data]
     * @param  array $billing  [Billing data]
     * @param  array $options  [Options data]
     * @return void
     */
    public function saleXml($merchant, $order, $payments, $billing, $options) {

        $this->header();

        $this->verificationNode($merchant['id'], $merchant['key']);

        $this->xml_writer->startElement('sale');

        $this->orderNode($order);

        $this->paymentsNode($payments);

        $this->billingNode($billing);

        $this->xml_writer->writeElement('urlReturn', $options['urlReturn']);
        $this->xml_writer->writeElement('fraud', $options['fraud']);
        $this->xml_writer->writeElement('customField', $options['customField']);

        $this->xml_writer->endElement();
    }

    /**
     * Card node
     *
     * @param  array $card [Creditcard data]
     * @return void
     */
    public function cardXML($card) {

        $this->xml_writer->writeElement('flag', Utils::formatSlug($card['flag']));
        $this->xml_writer->writeElement('cardHolder', $card['cardHolder']);
        $this->xml_writer->writeElement('cardNumber', Utils::formatNumber($card['cardNumber']));
        $this->xml_writer->writeElement('cardSecurityCode', Utils::formatNumber($card['cardSecurityCode']));
        $this->xml_writer->writeElement('cardExpirationDate', Utils::formatNumber($card['cardExpirationDate']));
        $this->xml_writer->writeElement('saveCreditCard', $card['saveCreditCard']);
        $this->xml_writer->writeElement('generateToken', $card['generateToken']);
        $this->xml_writer->writeElement('departureTax', $card['departureTax']);
        $this->xml_writer->writeElement('softDescriptor', $card['softDescriptor']);
    }

    /**
     * Payments nodes
     *
     * @param  array $payments [Payment data]
     * @return void
     */
    private function paymentsNode($payments) {

        if (isset($payments[0]) && is_array($payments[0])) {

            foreach ($payments as $key => $payment) {

                $this->xml_writer->startElement('payment');

                if (isset($payment['tokenCard']) && !empty($payment['tokenCard'])) {
                    $this->tokenCardXML($payment['tokenCard']);
                }

                $this->xml_writer->writeElement('acquirer', $payment['acquirer']);
                $this->xml_writer->writeElement('method', $payment['method']);
                $this->xml_writer->writeElement('amount', Utils::formatNumber($payment['amount']));
                $this->xml_writer->writeElement('currency', Config::$CURRENCIES['BRL']);
                $this->xml_writer->writeElement('country', $payment['country']);
                $this->xml_writer->writeElement('numberOfPayments', $payment['numberOfPayments']);
                $this->xml_writer->writeElement('groupNumber', $payment['groupNumber']);

                if (!isset($payment['tokenCard']) || empty($payment['tokenCard'])) {
                    $this->cardXML($payment);
                }

                $this->xml_writer->endElement();
            }
        } else {

            $this->xml_writer->startElement('payment');

            if (isset($payments['tokenCard']) && !empty($payments['tokenCard'])) {
                $this->tokenCardXML($payments['tokenCard']);
            }

            $this->xml_writer->writeElement('acquirer', $payments['acquirer']);
            $this->xml_writer->writeElement('method', $payments['method']);
            $this->xml_writer->writeElement('amount', Utils::formatNumber($payments['amount']));
            $this->xml_writer->writeElement('currency', Config::$CURRENCIES['BRL']);
            $this->xml_writer->writeElement('country', $payments['country']);
            $this->xml_writer->writeElement('numberOfPayments', $payments['numberOfPayments']);
            $this->xml_writer->writeElement('groupNumber', $payments['groupNumber']);

            if (!isset($payments['tokenCard']) || empty($payments['tokenCard'])) {
                $this->cardXML($payments);
            }

            $this->xml_writer->endElement();
        }
    }

    /**
     * Return the Token Card node
     *
     * @param  [String] $token [Token card saved]
     * @return void
     */
    public function tokenCardXML($token) {

        $this->xml_writer->writeElement('tokenCard', $token);
    }

    /**
     * Report XML
     *
     * @param  array  $merchant [Merchant data]
     * @param  [String] $tid      [AZPay transaction ID]
     * @return void
     */
    public function reportXml($merchant, $tid) {

        $this->header();

        $this->verificationNode($merchant['id'], $merchant['key']);

        $this->xml_writer->startElement('report');

        $this->xml_writer->writeElement('transactionId', $tid);

        $this->xml_writer->endElement();
    }

    /**
     * Cancel XML
     *
     * @param  array  $merchant [Merchant data]
     * @param  [String] $tid      [AZPay transaction ID]
     * @return void
     */
    public function cancelXml($merchant, $tid) {

        $this->header();

        $this->verificationNode($merchant['id'], $merchant['key']);

        $this->xml_writer->startElement('cancel');

        $this->xml_writer->writeElement('transactionId', $tid);

        $this->xml_writer->endElement();
    }

    /**
     * Boleto XML
     *
     * @param  array $merchant [Merchant data]
     * @param  array $order    [Order data]
     * @param  array $payment  [Payment data]
     * @param  array $billing  [Billing data]
     * @param  array $options  [Options data]
     * @return void
     */
    public function boletoXml($merchant, $order, $payment, $billing, $options) {

        $this->header();

        $this->verificationNode($merchant['id'], $merchant['key']);

        $this->xml_writer->startElement('boleto');

        $this->orderNode($order);

        $this->paymentBoletoNode($payment);

        $this->billingNode($billing);

        $this->xml_writer->writeElement('urlReturn', $options['urlReturn']);
        $this->xml_writer->writeElement('customField', $options['customField']);

        $this->xml_writer->endElement();
    }

    /**
     * Boleto payment node
     *
     * @param  array $payment [Payment data]
     * @return void
     */
    private function paymentBoletoNode($payment) {

        $this->xml_writer->startElement('payment');

        $this->xml_writer->writeElement('acquirer', $payment['acquirer']);
        $this->xml_writer->writeElement('expire', $payment['expire']);
        $this->xml_writer->writeElement('nrDocument', $payment['nrDocument']);
        $this->xml_writer->writeElement('amount', Utils::formatNumber($payment['amount']));
        $this->xml_writer->writeElement('currency', $payment['currency']);
        $this->xml_writer->writeElement('country', $payment['country']);
        $this->xml_writer->writeElement('instructions', $payment['instructions']);

        $this->xml_writer->endElement();
    }

    /**
     * PagSeguro XML
     *
     * @param  array $merchant [Merchant data]
     * @param  array $order    [Order data]
     * @param  array $payment  [Payment data]
     * @param  array $billing  [Billing data]
     * @param  array $options  [Options data]
     * @return void
     */
    public function pagseguroXml($merchant, $order, $payment, $billing, $options) {

        $this->header();

        $this->verificationNode($merchant['id'], $merchant['key']);

        $this->xml_writer->startElement('pagseguro');

        $this->orderNode($order);

        $this->paymentPagseguroNode($payment);

        $this->billingNode($billing);

        $this->xml_writer->writeElement('urlReturn', $options['urlReturn']);

        $this->xml_writer->endElement();
    }

    /**
     * PagSeguro payment node
     *
     * @param  array $payment [Payment data]
     * @return void
     */
    private function paymentPagseguroNode($payment) {

        $this->xml_writer->startElement('payment');

        $this->xml_writer->writeElement('amount', Utils::formatNumber($payment['amount']));
        $this->xml_writer->writeElement('currency', $payment['currency']);
        $this->xml_writer->writeElement('country', $payment['country']);

        $this->xml_writer->endElement();
    }

    /**
     * PagSeguro Checkout XML
     *
     * @param  array $merchant [Merchant data]
     * @param  array $order    [Order data]
     * @param  array $payment  [Payment data]
     * @param  array $billing  [Billing data]
     * @param  array $options  [Options data]
     * @return void
     */
    public function pagseguroCheckoutXml($merchant, $order, $payment, $billing, $options) {

        $this->header();

        $this->verificationNode($merchant['id'], $merchant['key']);

        $this->xml_writer->startElement('pagseguro_checkout');

        $this->orderNode($order);

        $this->paymentPagseguroCheckoutNode($payment);

        $this->billingPagseguroNode($billing);

        $this->xml_writer->writeElement('urlReturn', $options['urlReturn']);
        $this->xml_writer->writeElement('customField', $options['customField']);

        $this->xml_writer->endElement();
    }

    /**
     * PagSeguro Checkout payment node
     *
     * @param  array $payment [Payment data]
     * @return void
     */
    private function paymentPagseguroCheckoutNode($payment) {

        $this->xml_writer->startElement('payment');

        $this->xml_writer->writeElement('method', $payment['method']);
        $this->xml_writer->writeElement('amount', Utils::formatNumber($payment['amount']));
        $this->xml_writer->writeElement('currency', $payment['currency']);
        $this->xml_writer->writeElement('country', $payment['country']);
        $this->xml_writer->writeElement('numberOfPayments', $payment['numberOfPayments']);
        $this->xml_writer->writeElement('flag', $payment['flag']);
        $this->xml_writer->writeElement('cardHolder', $payment['cardHolder']);
        $this->xml_writer->writeElement('cardNumber', $payment['cardNumber']);
        $this->xml_writer->writeElement('cardSecurityCode', $payment['cardSecurityCode']);
        $this->xml_writer->writeElement('cardExpirationDate', $payment['cardExpirationDate']);

        $this->xml_writer->endElement();
    }

    /**
     * PagSeguro Checkout billing node
     *
     * @param  array $billing [Billing data]
     * @return void
     */
    private function billingPagseguroNode($billing) {

        $this->xml_writer->startElement('billing');

        $this->xml_writer->writeElement('customerIdentity', $billing['customerIdentity']);
        $this->xml_writer->writeElement('name', $billing['name']);
        $this->xml_writer->writeElement('address', $billing['address'] . ', ' . $billing['addressNumber']);
        $this->xml_writer->writeElement('address2', $billing['address2']);
        $this->xml_writer->writeElement('city', $billing['city']);
        $this->xml_writer->writeElement('state', $billing['state']);
        $this->xml_writer->writeElement('postalCode', Utils::formatNumber($billing['postalCode']));
        $this->xml_writer->writeElement('country', $billing['country']);
        $this->xml_writer->writeElement('phone', Utils::formatNumber($billing['phone']));
        $this->xml_writer->writeElement('email', $billing['email']);
        $this->xml_writer->writeElement('birthDate', $billing['birthDate']);
        $this->xml_writer->writeElement('cpf', $billing['cpf']);

        $this->xml_writer->endElement();
    }

    /**
     * PagSeguro XML
     *
     * @param  array $merchant [Merchant data]
     * @param  array $order    [Order data]
     * @param  array $payment  [Payment data]
     * @param  array $billing  [Billing data]
     * @param  array $options  [Options data]
     * @return void
     */
    public function fraudXml($billing, $options, $product) {

        $this->xml_writer->startElement('fraudData');

        $this->billingFraud($billing, $options);

        $this->productFraudNode($product);

        $this->xml_writer->endElement();
    }

    /**
     * PagSeguro Checkout billing node
     *
     * @param  array $billing [Billing data]
     * @return void
     */
    private function billingFraud($billing, $options) {

        $this->xml_writer->writeElement('operator', 'clearsale');
        $this->xml_writer->writeElement('method', 'start');
        $this->xml_writer->writeElement('costumerIP', $options['costumerIP']);
        $this->xml_writer->writeElement('document', $billing['customerIdentity']);
        $this->xml_writer->writeElement('name', $billing['name']);
        $this->xml_writer->writeElement('address', $billing['address']);
        $this->xml_writer->writeElement('addressNumber', $billing['addressNumber']);
        $this->xml_writer->writeElement('address2', $billing['address2']);
        $this->xml_writer->writeElement('city', $billing['city']);
        $this->xml_writer->writeElement('state', $billing['state']);
        $this->xml_writer->writeElement('postalCode', Utils::formatNumber($billing['postalCode']));
        $this->xml_writer->writeElement('country', $billing['country']);
        $this->xml_writer->writeElement('phonePrefix', Utils::formatNumber($billing['phonePrefix']));
        $this->xml_writer->writeElement('phoneNumber', Utils::formatNumber($billing['phoneNumber']));
        $this->xml_writer->writeElement('email', $billing['email']);
    }

    /**
     * Product nodes
     *
     * @param  array $product
     * @return void
     */
    public function productFraudNode($product) {

        $this->xml_writer->startElement('itens');
        if (isset($payments[0]) && is_array($payments[0])) {
            foreach ($product as $key => $product) {
                $this->xml_writer->startElement('item');
                $this->xml_writer->writeElement('productName', $product['productName']);
                $this->xml_writer->writeElement('quantity', $product['quantity']);
                $this->xml_writer->writeElement('price', $product['price']);
                $this->xml_writer->endElement();
            }
        } else {
            $this->xml_writer->startElement('item');
            $this->xml_writer->writeElement('productName', $product['productName']);
            $this->xml_writer->writeElement('quantity', $product['quantity']);
            $this->xml_writer->writeElement('price', Utils::formatNumber($product['price']));
            $this->xml_writer->endElement();
        }
        $this->xml_writer->endElement();
    }

    /**
     * PayPal XML
     *
     * @param  array $merchant [Merchant data]
     * @param  array $order    [Order data]
     * @param  array $payment  [Payment data]
     * @param  array $billing  [Billing data]
     * @param  array $options  [Options data]
     * @return void
     */
    public function paypalXml($merchant, $order, $payment, $billing, $options) {

        $this->header();

        $this->verificationNode($merchant['id'], $merchant['key']);

        $this->xml_writer->startElement('paypal');

        $this->orderNode($order);

        $this->paymentPaypalNode($payment);

        $this->billingNode($billing);

        $this->xml_writer->writeElement('urlReturn', $options['urlReturn']);
        $this->xml_writer->writeElement('customField', $options['customField']);

        $this->xml_writer->endElement();
    }

    /**
     * PayPal payment node
     *
     * @param  array $payment [Payment data]
     * @return void
     */
    private function paymentPaypalNode($payment) {

        $this->xml_writer->startElement('payment');

        $this->xml_writer->writeElement('amount', Utils::formatNumber($payment['amount']));
        $this->xml_writer->writeElement('currency', $payment['currency']);
        $this->xml_writer->writeElement('country', $payment['country']);

        $this->xml_writer->endElement();
    }

    /**
     * Online Debit XML
     *
     * @param  array $merchant [Merchant data]
     * @param  array $order    [Order data]
     * @param  array $payment  [Payment data]
     * @param  array $billing  [Billing data]
     * @param  array $options  [Options data]
     * @return void
     */
    public function onlineDebitXml($merchant, $order, $payment, $billing, $options) {

        $this->header();

        $this->verificationNode($merchant['id'], $merchant['key']);

        $this->xml_writer->startElement('onlineDebit');

        $this->orderNode($order);

        $this->paymentOnlineDebitNode($payment);

        $this->billingNode($billing);

        $this->xml_writer->writeElement('urlReturn', $options['urlReturn']);

        $this->xml_writer->endElement();
    }

    /**
     * Online Debit payment node
     *
     * @param  array $payment [Payment data]
     * @return void
     */
    private function paymentOnlineDebitNode($payment) {

        $this->xml_writer->startElement('payment');

        $this->xml_writer->writeElement('acquirer', $payment['acquirer']);

        $this->xml_writer->endElement();
    }

    /**
     * Boleto Rebill XML
     *
     * @param  array $merchant [Merchant data]
     * @param  array $order    [Order data]
     * @param  array $payment  [Payment data]
     * @param  array $billing  [Billing data]
     * @param  array $options  [Options data]
     * @param  array $rebill   [Rebill data]
     * @return void
     */
    public function boletoRebillXml($merchant, $order, $payment, $billing, $options, $rebill) {

        $this->header();

        $this->verificationNode($merchant['id'], $merchant['key']);

        $this->xml_writer->startElement('rebill');

        $this->orderNode($order, $rebill);

        $this->paymentBoletoRebillNode($payment);

        $this->billingNode($billing);

        $this->xml_writer->writeElement('urlReturn', $options['urlReturn']);
        $this->xml_writer->writeElement('customField', $options['customField']);

        $this->xml_writer->endElement();
    }

    /**
     * Boleto Rebill payment node
     *
     * @param  array $payment [Payment data]
     * @return void
     */
    private function paymentBoletoRebillNode($payment) {

        $this->xml_writer->startElement('paymentBoletoBancario');

        $this->xml_writer->writeElement('acquirer', $payment['acquirer']);
        $this->xml_writer->writeElement('amount', Utils::formatNumber($payment['amount']));
        $this->xml_writer->writeElement('currency', $payment['currency']);
        $this->xml_writer->writeElement('country', $payment['country']);
        $this->xml_writer->writeElement('instructions', $payment['instructions']);

        $this->xml_writer->endElement();
    }

    /**
     * Creditcard Rebill XML
     *
     * @param  array $merchant [Merchant data]
     * @param  array $order    [Order data]
     * @param  array $payment  [Payment data]
     * @param  array $billing  [Billing data]
     * @param  array $options  [Options data]
     * @param  array $rebill   [Rebill data]
     * @return void
     */
    public function creditcardRebillXml($merchant, $order, $payment, $billing, $options, $rebill) {

        $this->header();

        $this->verificationNode($merchant['id'], $merchant['key']);

        $this->xml_writer->startElement('rebill');

        $this->orderNode($order, $rebill);

        $this->paymentCreditcardRebillNode($payment);

        $this->billingNode($billing);

        $this->xml_writer->writeElement('urlReturn', $options['urlReturn']);
        $this->xml_writer->writeElement('customField', $options['customField']);

        $this->xml_writer->endElement();
    }

    /**
     * Boleto Rebill payment node
     *
     * @param  array $payment [Payment data]
     * @return void
     */
    private function paymentCreditcardRebillNode($payment) {

        $this->xml_writer->startElement('paymentCreditCard');

        $this->xml_writer->writeElement('acquirer', $payment['acquirer']);
        $this->xml_writer->writeElement('amount', Utils::formatNumber($payment['amount']));
        $this->xml_writer->writeElement('currency', $payment['currency']);
        $this->xml_writer->writeElement('country', $payment['country']);
        $this->xml_writer->writeElement('numberOfPayments', $payment['numberOfPayments']);
        $this->xml_writer->writeElement('groupNumber', $payment['groupNumber']);
        $this->xml_writer->writeElement('flag', $payment['flag']);
        $this->xml_writer->writeElement('cardHolder', $payment['cardHolder']);
        $this->xml_writer->writeElement('cardNumber', $payment['cardNumber']);
        $this->xml_writer->writeElement('cardSecurityCode', $payment['cardSecurityCode']);
        $this->xml_writer->writeElement('cardExpirationDate', $payment['cardExpirationDate']);
        $this->xml_writer->writeElement('saveCreditCard', $payment['saveCreditCard']);
        $this->xml_writer->writeElement('generateToken', $payment['generateToken']);
        $this->xml_writer->writeElement('departureTax', $payment['departureTax']);
        $this->xml_writer->writeElement('softDescriptor', $payment['softDescriptor']);

        $this->xml_writer->endElement();
    }

}

// END class XML_Requests
?>