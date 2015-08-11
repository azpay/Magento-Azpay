<?php


class Wemage_Azpay_Block_Info_Cc extends Mage_Payment_Block_Info_Cc
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('azpay/info/cc.phtml');
    }

      /**
     * @return string
     */
    public function getAzpayTransactionId()
    {
        return $this->getInfo()->getAzpayTransactionId();
    }



}
