<?php

require_once(Mage::getModuleDir('','Wemage_Azpay').'/lib/azpay.php');

/*var_dump(Mage::getUrl('azpay'));
exit;*/

class Wemage_Azpay_Model_Api extends  Mage_Payment_Model_Method_Abstract  {

    protected $_merchantId;
    protected $_merchantKey;

    public function __construct() {
        $this->_merchantId = Mage::helper('azpay')->getMerchantId();
        $this->_merchantKey = Mage::helper('azpay')->getMerchantKey();
    }

   /**
     * Set Merchant Id
     *
     * @param string $merchantId
     * @return Wemage_Azpay_Model_Api
     */
    public function setMerchantId($merchantId) {
        $this->_merchantId = $merchantId;
        return $this;
    }

    /**
     * Get Merchant Id
     *
     * @return string
     */
    public function getMerchantId() {
        if (!$this->_merchantId) {
            Mage::throwException(Mage::helper('azpay')->__('You need to configure Merchant Id before performing requests.'));
        }
        return $this->_merchantId;
    }

    /**
     * Set Merchant Key
     *
     * @param string $merchantKey
     * @return Wemage_Azpay_Model_Api
     */
    public function setMerchantKey($merchantKey) {
        $this->_merchantKey = $merchantKey;
        return $this;
    }

    /**
     * Get Merchant Key
     *
     * @return string
     */
    public function getMerchantKey() {
        if (!$this->_merchantKey) {
            Mage::throwException(Mage::helper('azpay')->__('You need to configure Merchant Key before performing requests.'));
        }
        return $this->_merchantKey;
    }
}
