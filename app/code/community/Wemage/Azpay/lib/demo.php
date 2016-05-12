<?php 
date_default_timezone_set('America/Sao_Paulo');

include 'azpay.php';

// Instantiate AZPay
$azpay = new AZPay('00053232', 'xl4fgdwt6ukutyb99772555939853191');
$azpay->curl_timeout = 10;


/*=============================
=            Order            =
=============================*/

$azpay->config_order['reference'] = '123456789';
$azpay->config_order['totalAmount'] = '1000';

$azpay->config_rebill['period'] = '1';
$azpay->config_rebill['frequency'] = '15';
$azpay->config_rebill['dateStart'] = date('Y-m-d', strtotime('today + 1 day'));
$azpay->config_rebill['dateEnd'] = date('Y-m-d', strtotime('today + 1 year'));

/*-----  End of Order  ------*/


/*===============================
=            Billing            =
===============================*/

$azpay->config_billing['customerIdentity'] = '1';
$azpay->config_billing['name'] = 'Fulano de Tal';
$azpay->config_billing['address'] = 'Av. Federativa, 230';
$azpay->config_billing['address2'] = '10 Andar';
$azpay->config_billing['city'] = 'Mogi das Cruzes';
$azpay->config_billing['state'] = 'SP';
$azpay->config_billing['postalCode'] = '20031-170';
$azpay->config_billing['phone'] = '21 4009-9400';
$azpay->config_billing['email'] = 'fulanodetal@email.com';

/*-----  End of Billing  ------*/


/*==================================
=            CreditCard            =
==================================*/

$azpay->config_card_payments['acquirer'] = Config::$CARD_OPERATORS['cielo']['modes']['store']['code'];
$azpay->config_card_payments['method'] = '1';
$azpay->config_card_payments['amount'] = '1000';
$azpay->config_card_payments['currency'] = Config::$CURRENCIES['BRL'];
$azpay->config_card_payments['numberOfPayments'] = '1';
$azpay->config_card_payments['groupNumber'] = '0';
$azpay->config_card_payments['flag'] = 'visa';
$azpay->config_card_payments['cardHolder'] = 'José da Silva';
$azpay->config_card_payments['cardNumber'] = '4000000000010001';
$azpay->config_card_payments['cardSecurityCode'] = '123';
$azpay->config_card_payments['cardExpirationDate'] = '201805';
$azpay->config_card_payments['saveCreditCard'] = 'true';

/*-----  End of CreditCard  ------*/


/*==============================
=            Boleto            =
==============================*/

$azpay->config_boleto['acquirer'] = Config::$BOLETO_OPERATORS['bradesco']['code'];
$azpay->config_boleto['expire'] = date('Y-m-d', strtotime('today + 10 day'));
$azpay->config_boleto['nrDocument'] = '12345678';
$azpay->config_boleto['amount'] = '1000';
$azpay->config_boleto['currency'] = Config::$CURRENCIES['BRL'];
$azpay->config_boleto['instructions'] = 'Não aceitar pagamento em cheques. \n Percentual Juros Dia: 1%. Percentual Multa: 1%.';

/*-----  End of Boleto  ------*/



/*=================================
=            PagSeguro            =
=================================*/

// Simple mode
$azpay->config_pagseguro['amount'] = '1000';

// Checkout mode
$azpay->config_pagseguro_checkout['method'] = '1';
$azpay->config_pagseguro_checkout['amount'] = '1000';
$azpay->config_pagseguro_checkout['currency'] = Config::$CURRENCIES['BRL'];
$azpay->config_pagseguro_checkout['numberOfPayments'] = '1';
$azpay->config_pagseguro_checkout['flag'] = 'visa';
$azpay->config_pagseguro_checkout['cardHolder'] = 'José da Silva';
$azpay->config_pagseguro_checkout['cardNumber'] = '4000000000010001';
$azpay->config_pagseguro_checkout['cardSecurityCode'] = '123';
$azpay->config_pagseguro_checkout['cardExpirationDate'] = '201805';

// Billing to checkout mode
$azpay->config_pagseguro_checkout_billing['customerIdentity'] = '1';
$azpay->config_pagseguro_checkout_billing['name'] = 'Fulano de Tal';
$azpay->config_pagseguro_checkout_billing['address'] = 'Av. Federativa, 230';
$azpay->config_pagseguro_checkout_billing['address2'] = '10 Andar';
$azpay->config_pagseguro_checkout_billing['city'] = 'Mogi das Cruzes';
$azpay->config_pagseguro_checkout_billing['state'] = 'SP';
$azpay->config_pagseguro_checkout_billing['postalCode'] = '20031-170';
$azpay->config_pagseguro_checkout_billing['phone'] = '21 4009-9400';
$azpay->config_pagseguro_checkout_billing['email'] = 'fulanodetal@email.com';
$azpay->config_pagseguro_checkout_billing['birthDate'] = '20/05/1980';
$azpay->config_pagseguro_checkout_billing['cpf'] = '12345678910';

/*-----  End of PagSeguro  ------*/


/*==============================
=            PayPal            =
==============================*/

$azpay->config_paypal['amount'] = '1000';

/*-----  End of PayPal  ------*/


/*====================================
=            Online Debit            =
====================================*/

$azpay->config_online_debit['acquirer'] = '1';

/*-----  End of Online Debit  ------*/


/*===============================
=            Options            =
===============================*/

$azpay->config_options['urlReturn'] = 'http://loja.exemplo.com.br';
$azpay->config_options['fraud'] = 'false';
$azpay->config_options['customField'] = '';

/*-----  End of Options  ------*/



try {

	/*==========  Boleto  ==========*/
	//$azpay->boleto()->execute();

	/*==========  Authorization  ==========*/
	# Request authorization to transaction
	//$azpay->authorize()->execute();

	/*==========  Capture  ==========*/
	# Execute capture of transaction
	//$azpay->capture("7C39F328-3CE0-3CB6-C892-923FB7586D11")->execute();

	/*==========  Sale  ==========*/
	# Execute authorization and capture (direct sale), to transaction
	//$azpay->sale()->execute();

	/*==========  Cancel  ==========*/
	//$azpay->cancel("DDCE12F2-21A9-1E7D-B3B2-4B3867BEB6DF")->execute();

	/*==========  PagSeguro  ==========*/
	//$azpay->pagseguro()->execute();

	/*==========  Pagseguro  ==========*/
	//$azpay->pagseguro_checkout()->execute();

	/*==========  PayPal  ==========*/
	//$azpay->paypal()->execute();

	/*==========  Online Debit  ==========*/
	//$azpay->online_debit()->execute();

	/*==========  Rebill  ==========*/
	//$azpay->rebill()->execute();

	/*==========  Report  ==========*/
	# Check a transaction status by TID
	$azpay->report('105F866-67CD-4B89-BE82-B1AC33E7027F')->execute();


	/*==========  Response  ==========*/
	$xml = $azpay->response();

} catch (AZPay_Error $e) { // HTTP 409 - AZPay Error

	/*==========  Error Response  ==========*/	
	$error = $azpay->responseError();

} catch (AZPay_Curl_Exception $e) { // Connection Error
	print($e->getMessage());

} catch (AZPay_Exception $e) { // General
	print($e->getMessage());
}
?>