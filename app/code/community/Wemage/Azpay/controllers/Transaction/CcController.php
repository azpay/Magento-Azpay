<?php

class Wemage_Azpay_Transaction_CcController extends Mage_Core_Controller_Front_Action {

    public function postbackAction() {
        
        //$azpay = Mage::getModel('azpay/api');
        $request = $this->getRequest();

        if ( isset($_GET['TransactionID']) && !empty($_GET['TransactionID']) ) {
			
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
				$order->setData('state', "canceled");
		        $order->setStatus("canceled");
				$history = $order->addStatusHistoryComment('AZPay: Transação cancelada.', false);
		        $history->setIsCustomerNotified(false);
		        $order->save();
			}

			if ($callback['status'] == '8') {
				$order->setData('state', "complete");
		        $order->setStatus("complete");       
		        $history = $order->addStatusHistoryComment('AZPay: Transação capturada.', false);
		        $history->setIsCustomerNotified(false);
        		$order->save();
			}

			die(0);
		}
		
		$this->_forward('404');

    }

}
