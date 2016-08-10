<?php
/**
 *
 */
class Wemage_Azpay_Block_Info_Boleto extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('azpay/info/boleto.phtml');
    }
    

    /**
     * @return string
     */
    public function getBoletoUrl()
    {
        return $this->getInfo()->getAzpayBoletoUrl();
    }
    /**
     * @return string
     */
    public function getAzpayTransactionId()
    {
        return $this->getInfo()->getAzpayTransactionId();
    }

}
