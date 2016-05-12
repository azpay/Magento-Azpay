<?php

class Wemage_Azpay_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getOrderIdByTransactionId($tid) {
        $resource = Mage::getSingleton('core/resource');
        $conn = $resource->getConnection('core_read');
        $select = $conn->select()
                ->from($resource->getTableName('sales/order_payment'))
                ->where('azpay_transaction_id = ?', $tid)
                ->reset(Zend_Db_Select::COLUMNS)
                ->columns('parent_id');
        return $conn->fetchOne($select);
    }

    public function formatAmount($amount) {
        return number_format($amount, 2, '', '');
    }

    public function splitTelephone($telephone) {
        $telephone = Zend_Filter::filterStatic($telephone, 'Digits');
        $ddd = substr($telephone, 0, 2);
        $number = substr($telephone, 2);
        $data = array(
            'ddd' => $ddd,
            'number' => $number
        );
        return $data;
    }

    public function getMerchantId() {
        $merchantId = Mage::getStoreConfig('payment/azpay_setting/merchant_id');
        return $merchantId;
    }

    public function getMerchantKey() {
        $merchantKey = Mage::getStoreConfig('payment/azpay_setting/merchant_key');
        return $merchantKey;
    }

}
