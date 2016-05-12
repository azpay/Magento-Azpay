<?php

class Wemage_Azpay_Model_Source_Operators

{
    public function toOptionArray()
    {
        return array(
            array('value' => '10','label'=> Mage::helper('azpay')->__('Bradesco (sem registro)')),
            array('value' => '18','label'=> Mage::helper('azpay')->__('BradescoNET')),
            array('value' => '11','label'=> Mage::helper('azpay')->__('Itaú (sem registro)')),
            array('value' => '20','label'=> Mage::helper('azpay')->__('Itaú Shopline')),
            array('value' => '12','label'=> Mage::helper('azpay')->__('Banco do Brasil')),
            array('value' => '13','label'=> Mage::helper('azpay')->__('Banco Santander')),
            array('value' => '14','label'=> Mage::helper('azpay')->__('Caixa (sem registro)')),
            array('value' => '15','label'=> Mage::helper('azpay')->__('Caixa (Sinco)')),
            array('value' => '16','label'=> Mage::helper('azpay')->__('Caixa (SIGCB)')),
            array('value' => '17','label'=> Mage::helper('azpay')->__('HSBC')),
        );
    }

}
