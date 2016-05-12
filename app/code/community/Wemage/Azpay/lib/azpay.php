<?php

/**
 * AZPay SDK
 *
 * Software Development Kit to integrate a checkout with AZPay Gateway
 *
 * @author Gabriel Guerreiro <gabrielguerreiro.com>
 * @version 1.2.6
 * */
include 'config.php';
include 'utils.php';
include 'xml_requests.php';

class AZPay {

    /**
     * VERSION
     */
    const VERSION = '1.2.6';

    /**
     * Client Key and Client ID
     *
     * @var array
     */
    public $merchant = array(
        'id' => '',
        'key' => ''
    );

    /**
     * Order Details
     *
     * @var array
     */
    public $config_order = array(
        'reference' => '',
        'totalAmount' => '0000'
    );

    /**
     * Rebill configuration
     *
     * @var array
     */
    public $config_rebill = array(
        'period' => '1',
        'frequency' => '12',
        'dateStart' => '2015-01-01',
        'dateEnd' => '2014-01-01'
    );

    /**
     * Billing Details
     *
     * @var array
     */
    public $config_billing = array(
        'customerIdentity' => '',
        'name' => '',
        'address' => '',
        'addressNumber' => '',
        'address2' => '',
        'city' => '',
        'state' => '',
        'postalCode' => '',
        'country' => 'BR',
        'phonePrefix' => '',
        'phoneNumber' => '',
        'email' => ''
    );
    
    /**
     * Product Details
     *
     * @var array
     */
    public $config_product = array(
        'productName' => '',
        'quantity' => '',
        'price' => ''
    );

    /**
     * Creditcard data
     *
     * @var array
     */
    public $config_card_payments = array(
        'acquirer' => '',
        'method' => '',
        'amount' => '0000',
        'currency' => '986',
        'country' => 'BRA',
        'numberOfPayments' => '1',
        'groupNumber' => '0',
        'flag' => '',
        'cardHolder' => '',
        'cardNumber' => '',
        'cardSecurityCode' => '',
        'cardExpirationDate' => '',
        'saveCreditCard' => 'true',
        'generateToken' => 'false',
        'departureTax' => '0',
        'softDescriptor' => '',
        'tokenCard' => ''
    );

    /**
     * Boleto configuration
     *
     * @var array
     */
    public $config_boleto = array(
        'acquirer' => '10',
        'expire' => '',
        'nrDocument' => '',
        'amount' => '0000',
        'currency' => '986',
        'country' => 'BRA',
        'instructions' => ''
    );

    /**
     * PagSeguro configuration
     *
     * @var array
     */
    public $config_pagseguro = array(
        'amount' => '0000',
        'currency' => '986',
        'country' => 'BRA'
    );

    /**
     * PagSeguro Creditcard configuration
     *
     * @var array
     */
    public $config_pagseguro_checkout = array(
        'method' => '',
        'amount' => '0000',
        'currency' => '986',
        'country' => 'BRA',
        'numberOfPayments' => '1',
        'flag' => '',
        'cardHolder' => '',
        'cardNumber' => '',
        'cardSecurityCode' => '',
        'cardExpirationDate' => ''
    );

    /**
     * Billing Details to PagSeguro
     *
     * @var array
     */
    public $config_pagseguro_checkout_billing = array(
        'customerIdentity' => '',
        'name' => '',
        'address' => '',
        'address2' => '',
        'city' => '',
        'state' => '',
        'postalCode' => '',
        'country' => 'BR',
        'phone' => '',
        'email' => '',
        'birthDate' => '',
        'cpf' => ''
    );

    /**
     * PayPal configuration
     *
     * @var array
     */
    public $config_paypal = array(
        'amount' => '0000',
        'currency' => '986',
        'country' => 'BRA'
    );

    /**
     * Online Debit configuration
     *
     * @var array
     */
    public $config_online_debit = array(
        'acquirer' => ''
    );

    /**
     * Options extra
     *
     * @var array
     */
    public $config_options = array(
        'urlReturn' => '',
        'fraud' => 'false',
        'customField' => ''
    );

    /**
     * Reponse
     *
     * @var String
     */
    private $curl_response = null;

    /**
     * Meta data
     *
     * @var String
     */
    private $curl_response_meta = null;

    /**
     * Errors
     *
     * @var String
     */
    private $curl_error = null;

    /**
     * Errors Code
     *
     * @var int
     */
    private $curl_error_code = 0;

    /**
     * Set timeout to cURL
     * default = 5s
     *
     * @var int
     */
    public $curl_timeout = 5;

    /**
     * Error flag
     *
     * @var boolean
     */
    public $error = false;

    /**
     * Flag to execute Exceptions
     *
     * @var boolean
     */
    private $throw_exceptions = true;

    /**
     * XML generated
     * @var XMLObject
     */
    private $xml = null;

    /**
     * Construct Class
     *
     * @param string $merchant_id  [Client ID]
     * @param string $merchant_key [Client Key]
     */
    function __construct($merchant_id, $merchant_key) {

        if (!function_exists('curl_init'))
            throw new Exception('cURL module is not available! This SDK requires cURL. See http://php.net/manual/en/book.curl.php');

        $this->merchant['id'] = $merchant_id;
        $this->merchant['key'] = $merchant_key;
    }

    /**
     * Execute Request
     * Use after all to make request in fact
     *
     * eg: $azpay->sale()->execute()
     *
     * @return this
     */
    public function execute() {

        // Reset error flag
        $this->error = false;

        // Init cURL
        $ch = curl_init();



        // Config cURL
        curl_setopt($ch, CURLOPT_URL, Config::$RECEIVER_URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);



        $this->curl_response = curl_exec($ch);
        $this->curl_response_meta = curl_getinfo($ch);

        if ($this->curl_response === false || $this->curl_response_meta === false) {
            $this->error = true;
            $this->curl_error = curl_error($ch);
            $this->curl_error_code = curl_errno($ch);
        }

        if ($this->curl_response === false && $this->throw_exceptions)
            throw new AZPay_Curl_Exec_Exception(curl_error($ch));

        if ($this->curl_response_meta === false && $this->throw_exceptions)
            throw new AZPay_Curl_Meta_Exception(curl_error($ch));

        curl_close($ch);

        $this->checkErrors();

        return $this;
    }

    /**
     * Return XML response
     * from AZPay
     *
     * @return XMLObject if [Reponse]
     * @return string if [Error]
     */
    public function response() {

        // If no errors, return XML parsed
        if ($this->curl_error === null) {

            return simplexml_load_string($this->curl_response, 'SimpleXMLElement', LIBXML_NOCDATA);
        } else {

            return $this->curl_error;
        }
    }

    /**
     * Get AZPay errors response
     * use inside Catch
     * 
     *
     * @return array [Data from errors]
     */
    public function responseError() {

        $xml = simplexml_load_string($this->curl_response, 'SimpleXMLElement', LIBXML_NOCDATA);
        $json = json_decode(json_encode($xml));
        $response = array(
            'status_code' => null,
            'status_message' => null,
            'error_code' => null,
            'error_action' => null,
            'error_message' => null,
            'error_details' => null,
            'error_moreInfo' => null,
            'message_acquirer' => null,
            'error_acquirer' => null,
            'timestamp' => null
        );

        if (property_exists($json, 'status')) {

            $response['status_code'] = (property_exists($json->status, 'code')) ? $json->status->code : null;
            $response['status_message'] = (property_exists($json->status, 'message')) ? $json->status->message : null;
        }

        if (property_exists($json, 'result') && property_exists($json->result, 'error')) {

            $response['error_code'] = (property_exists($json->result->error, 'code')) ? $json->result->error->code : null;
            $response['error_action'] = (property_exists($json->result->error, 'action')) ? $json->result->error->action : null;
            $response['error_message'] = (property_exists($json->result->error, 'code') && isset(Config::$ERROR_MESSAGE[$json->result->error->code])) ? Config::$ERROR_MESSAGE[$json->result->error->code] : null;
            $response['error_details'] = (property_exists($json->result->error, 'details') && is_string($json->result->error->details)) ? str_replace(array('<![CDATA[', ']]>'), '', $json->result->error->details) : null;
            $response['error_moreInfo'] = (property_exists($json->result->error, 'moreInfo') && is_string($json->result->error->moreInfo)) ? str_replace(array('<![CDATA[', ']]>'), '', $json->result->error->moreInfo) : null;

            // 101 - XML Error
            if (property_exists($json->result->error, 'code') && property_exists($json->result->error, 'message') && intval($json->result->error->code) !== 101) {
                $response['message_acquirer'] = (property_exists($json->result->error->message, 'acquirer')) ? $json->result->error->message->acquirer : null;
                $response['error_acquirer'] = (property_exists($json->result->error->message, 'errorAcquirer')) ? $json->result->error->message->errorAcquirer : null;
                $response['timestamp'] = (property_exists($json->result->error->message, 'timestamp')) ? $json->result->error->message->timestamp : null;
            }
        }

        return $response;
    }

    /**
     * Get XML generated
     * Use after any Request and before Execute
     *
     * eg: $azpay->sale()->getXml()
     *
     * @return XMLObject
     */
    public function getXml() {
        return $this->xml;
    }

    /**
     * Get cURL response
     * 
     * @return string
     */
    public function getCurlResponse() {
        return $this->curl_response;
    }

    /**
     * Get cURL response meta
     * 
     * @return string
     */
    public function getCurlResponseMeta() {
        return $this->curl_response_meta;
    }

    /**
     * Get cURL Error
     * 
     * @return string
     */
    public function getCurlError() {
        return $this->curl_error;
    }

    /**
     * Get cURL Error Code
     * 
     * @return string
     */
    public function getCurlErrorCode() {
        return $this->curl_error_code;
    }

    /**
     * Flag to ignore exceptions
     * user before any Request
     *
     * eg: $azpay->ignoreExceptions()->sale()
     * 
     * @return this
     */
    public function ignoreExceptions() {
        $this->throw_exceptions = false;
        return $this;
    }

    /**
     * Request authorization
     * to complete the transaction
     *
     * Require:
     * 	- $merchant
     * 	- $config_order
     * 	- $config_card_payments
     * 	- $config_billing
     * 	- $config_options
     *
     * @return this
     */
    public function authorize() {

        $requests = new XML_Requests();


        $requests->authorizeXml($this->merchant, $this->config_order, $this->config_card_payments, $this->config_billing, $this->config_options, $this->config_product);

        $this->xml = $requests->output();

        return $this;
    }

    /**
     * Request the capture of the transaction
     * after the authorization request
     * to payment validation
     *
     * Require:
     * 	- $merchant
     *
     * @param  string $transactionId [TID of AZPay transaction]
     * @return this
     */
    public function capture($transactionId) {

        $requests = new XML_Requests();

        $requests->captureXml($this->merchant['id'], $this->merchant['key'], $transactionId);
        $this->xml = $requests->output();

        return $this;
    }

    /**
     * Request Sale
     * without a pre authorization
     *
     * Require:
     *  - $merchant
     *  - $config_order
     *  - $config_card_payments
     *  - $config_billing
     *  - $config_options
     *
     * @return this
     */
    public function sale() {

        $requests = new XML_Requests();

        $requests->saleXml($this->merchant, $this->config_order, $this->config_card_payments, $this->config_billing, $this->config_options);
        $this->xml = $requests->output();

        return $this;
    }

    /**
     * Request Rebill
     *
     * Require:
     *  - $merchant
     *  - $config_order
     *  - $config_card_payments
     *  - $config_billing
     *  - $config_options
     *  - $config_rebill
     *
     * @return this
     */
    public function rebill() {

        $requests = new XML_Requests();

        $requests->creditcardRebillXml($this->merchant, $this->config_order, $this->config_card_payments, $this->config_billing, $this->config_options, $this->config_rebill);
        $this->xml = $requests->output();

        return $this;
    }

    /**
     * Request a transaction status report
     *
     * Require:
     * 	- $merchant
     *
     * @param  string $transactionId [TID of AZPay transaction]
     * @return this
     */
    public function report($transactionID) {

        $requests = new XML_Requests();

        $requests->reportXml($this->merchant, $transactionID);
        $this->xml = $requests->output();

        return $this;
    }

    /**
     * Request a Transaction cancel
     *
     * Require:
     * 	- $merchant
     *
     * @param  string $transactionID [TID of AZPay transaction]
     * @return this
     */
    public function cancel($transactionID) {

        $requests = new XML_Requests();

        $requests->cancelXml($this->merchant, $transactionID);
        $this->xml = $requests->output();

        return $this;
    }

    /**
     * Request Boleto
     *
     * Require:
     * 	- $merchant
     *  - $config_order
     *  - $config_boleto
     *  - $config_billing
     *  - $config_options
     *
     * To Rebill require:
     * 	- $config_rebill
     *
     * @param  [boolean] $rebill [Flag to enable rebill]
     * @return this
     */
    public function boleto($rebill = false) {

        $requests = new XML_Requests();

        if ($rebill) {
            $requests->boletoRebillXml($this->merchant, $this->config_order, $this->config_boleto, $this->config_billing, $this->config_options, $this->config_rebill);
        } else {
            $requests->boletoXml($this->merchant, $this->config_order, $this->config_boleto, $this->config_billing, $this->config_options);
        }

        $this->xml = $requests->output();

        return $this;
    }

    /**
     * PagSeguro
     *
     * Require:
     *  - $merchant
     *  - $config_order
     *  - $config_pagseguro
     *  - $config_billing
     *  - $config_options
     *
     * @return this
     */
    public function pagseguro() {

        $requests = new XML_Requests();

        $requests->pagseguroXml($this->merchant, $this->config_order, $this->config_pagseguro, $this->config_billing, $this->config_options);
        $this->xml = $requests->output();

        return $this;
    }

    /**
     * PagSeguro Checkout
     *
     * Require:
     *  - $merchant
     *  - $config_order
     *  - $config_pagseguro_checkout
     *  - $config_pagseguro_billing
     *  - $config_options
     *
     * @return this
     */
    public function pagseguro_checkout() {

        $requests = new XML_Requests();

        $requests->pagseguroCheckoutXml($this->merchant, $this->config_order, $this->config_pagseguro_checkout, $this->config_pagseguro_checkout_billing, $this->config_options);
        $this->xml = $requests->output();

        return $this;
    }

    /**
     * PayPal Checkout
     *
     * Require:
     *  - $merchant
     *  - $config_order
     *  - $config_paypal
     *  - $config_billing
     *  - $config_options
     *
     * @return this
     */
    public function paypal() {

        $requests = new XML_Requests();

        $requests->paypalXml($this->merchant, $this->config_order, $this->config_paypal, $this->config_billing, $this->config_options);
        $this->xml = $requests->output();

        return $this;
    }

    /**
     * Online Debit
     *
     * Require:
     *  - $merchant
     *  - $config_order
     *  - $config_pagseguro_checkout
     *  - $config_pagseguro_billing
     *  - $config_options
     *
     * @return this
     */
    public function online_debit() {

        $requests = new XML_Requests();

        $requests->onlineDebitXml($this->merchant, $this->config_order, $this->config_online_debit, $this->config_billing, $this->config_options);
        $this->xml = $requests->output();

        return $this;
    }

    /**
     * Check error exceptions
     *
     * @return void
     */
    private function checkErrors() {

        if (!$this->throw_exceptions)
            return;

        $meta = $this->curl_response_meta;
        $response = $this->curl_response;

        if ($meta === false)
            return;

        switch ($meta['http_code']) {
            case 400:
                throw new AZPay_BadRequest($response);
                break;
            case 401:
                throw new AZPay_Unauthorized($response);
                break;
            case 403:
                throw new AZPay_Forbidden($response);
                break;
            case 404:
                throw new AZPay_NotFound($response);
                break;
            case 405:
                throw new AZPay_MethodNotAllowed($response);
                break;
            case 409:
                throw new AZPay_Error($response);
                break;
            case 410:
                throw new AZPay_Gone($response);
                break;
            case 422:
                throw new AZPay_InvalidRecord($response);
                break;
            default:
                if ($meta['http_code'] >= 400 && $meta['http_code'] <= 499) {
                    throw new AZPay_ClientError($response);
                } elseif ($meta['http_code'] >= 500 && $meta['http_code'] <= 599) {
                    throw new AZPay_ServerError($response);
                } elseif (!isset($meta['http_code']) || $meta['http_code'] >= 600) {
                    throw new AZPay_UnknownResponse($response);
                }
        }
    }

}

/**
 * General Exceptions
 */
class AZPay_Exception extends Exception {
    
}

class AZPay_Unknown_Exception extends AZPay_Exception {
    
}

/**
 * Client Exception
 */
class AZPay_Client_Exception extends AZPay_Exception {
    
}

/**
 * HTTP Exceptions
 */
class AZPay_BadRequest extends AZPay_Client_Exception {
    
}

class AZPay_Unauthorized extends AZPay_Client_Exception {
    
}

class AZPay_Forbidden extends AZPay_Client_Exception {
    
}

class AZPay_NotFound extends AZPay_Client_Exception {
    
}

class AZPay_MethodNotAllowed extends AZPay_Client_Exception {
    
}

class AZPay_Error extends AZPay_Client_Exception {
    
}

class AZPay_Gone extends AZPay_Client_Exception {
    
}

class AZPay_InvalidRecord extends AZPay_Client_Exception {
    
}

class AZPay_ServerError extends AZPay_Client_Exception {
    
}

class AZPay_UnknownResponse extends AZPay_Client_Exception {
    
}

/**
 * cUrl Exceptions
 */
class AZPay_Curl_Exception extends AZPay_Exception {
    
}

class AZPay_Curl_Init_Exception extends AZPay_Curl_Exception {
    
}

class AZPay_Curl_Meta_Exception extends AZPay_Curl_Exception {
    
}

class AZPay_Curl_Exec_Exception extends AZPay_Curl_Exception {
    
}

?>