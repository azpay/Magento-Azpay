<?php

class Wemage_Azpay_Block_Checkout_Success_Payment_Boleto extends Wemage_Azpay_Block_Checkout_Success_Payment_Default {

    public function getBoletoUrl() {

      $order = $this->getOrder();
      $resource = Mage::getSingleton('core/resource');
    	$readConnection = $resource->getConnection('core_read');
    	$query = 'SELECT additional_data FROM sales_flat_order_payment WHERE entity_id = '.$order->getId().' LIMIT 1';
    	$queryResult = unserialize($readConnection->fetchOne($query));

      return $queryResult['azpayboletourl'];

    }

}
