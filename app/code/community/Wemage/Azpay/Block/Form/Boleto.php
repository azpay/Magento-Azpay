<?php

class Wemage_Azpay_Block_Form_Boleto extends Mage_Payment_Block_Form
{
    protected $_instructions;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('azpay/form/boleto.phtml');
    }

    public function getInstructions()
    {
        if (is_null($this->_instructions)) {
            $this->_instructions = $this->getMethod()->getConfigData('instructions');
        }
        return $this->_instructions;
    }
    
    
}
