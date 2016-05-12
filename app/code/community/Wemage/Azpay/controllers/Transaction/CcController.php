<?php

class Wemage_Azpay_Transaction_CcController extends Mage_Core_Controller_Front_Action {

	public static function send($text='',$channel="yebo-aidax") {

		$data_string = array(
		  'channel'=>"#".$channel,
		  'username'=>"AZPAY MAGENTO PLUGIN",
		  'text'=>$text,
		  'icon_emoji'=>':az:'
		  //'icon_url'=>'http://doc.azpay.com.br/images/logo.png',
		);

		$data_string = json_encode($data_string);
		$ch = curl_init('https://hooks.slack.com/services/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');                                                                      
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
		    'Content-Type: application/json',                                                                                
		    'Content-Length: ' . strlen($data_string))                                                                       
		);                                                                                                                   
		 
		$result = curl_exec($ch);
		//var_dump($result);exit;
	}

    public function postbackAction() {

    	self::send('start postbackAction');
        
        //$azpay = Mage::getModel('azpay/api');
        $request = $this->getRequest();

        if ( isset($_GET['TransactionID']) && !empty($_GET['TransactionID']) ) {

        	self::send('get transactionID');
			
        	$callback = array(
				'tid' 			  => $_POST['TID'],
				'order_reference' => $_POST['order_reference'],
				'customers_id' 	  => $_POST['customers_id'],
				'status' 		  => $_POST['status'],
			);

        	// get Order
			$orderId = intval($callback['order_reference']);
			$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);

			if ($callback['status'] == '6') {
				self::send('status cancelled');
				$order->setData('state', "canceled");
		        $order->setStatus("canceled");
				$history = $order->addStatusHistoryComment('AZPay: Transação cancelada.', false);
		        $history->setIsCustomerNotified(false);
		        $order->save();
			}

			if ($callback['status'] == '8') {
				self::send('status approved');
				$order->setData('state', "complete");
		        $order->setStatus("complete");       
		        $history = $order->addStatusHistoryComment('AZPay: Transação capturada.', false);
		        $history->setIsCustomerNotified(false);
        		$order->save();
			}

			self::send('finish postbackAction');

			die(0);
		}
		
		$this->_forward('404');

    }

}
