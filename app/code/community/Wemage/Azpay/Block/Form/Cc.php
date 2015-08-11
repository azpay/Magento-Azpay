<?php

class Wemage_Azpay_Block_Form_Cc extends Mage_Payment_Block_Form_Cc {

    const MIN_INSTALLMENT_VALUE = 5;

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('azpay/form/cc.phtml');
    }

    public function getCcMonths() {
        $months = $this->getData('cc_months');
        if (is_null($months)) {
         //   $months[0] = $this->__('Month');
            for ($i = 1; $i <= 12; $i++) {
                $months[$i] = str_pad($i, 2, '0', STR_PAD_LEFT);
            }
            $this->setData('cc_months', $months);
        }
        return $months;
    }
    
    

    public function getInstallmentsAvailables() {
        $pt = (int) Mage::getStoreConfig('payment/azpay_cc/max_installments');
        $vmp = (float) Mage::getStoreConfig('payment/azpay_cc/min_installment_value');
        $tj = (float) Mage::getStoreConfig('payment/azpay_cc/interest_rate');
        $ps = (int) Mage::getStoreConfig('payment/azpay_cc/free_installments'); // qtd de parcelas sem juros;

        $quote = Mage::helper('checkout')->getQuote();
        $gt = $quote->getGrandTotal();

        $n = floor($gt / $vmp);
        if ($n > $pt) {
            $n = $pt;
        } elseif ($n < 1) {
            $n = 1;
        }

        $parcelas = array();
        for ($i = 0; $i < $n; $i++) {
     
            if ($i + 1 == 1) {
                $label = '1x - ' . $this->helper('checkout')->formatPrice($gt);
            } else {
                $label = ($i + 1) . 'x - ' . $this->helper('checkout')->formatPrice($gt / ($i + 1));
            }
            $parcelas[] = array('parcela' => $i + 1, 'label' => $label);
        }

        //print_r($parcelas);exit;

        return $parcelas;
    }

    /**
     * Retrieve availables credit card types
     * Mage_Payment_Block_Form_Cc
     * @return array
     *   
     */
    public function getCcAvailableTypes() {
        $availableTypes = explode(",", Mage::getStoreConfig('payment/azpay_cc/cctypes'));
        $types = Mage::getModel('azpay/source_cctypes')->toOptionArray();

        foreach ($types as $type) {
            if (!in_array($type['value'], $availableTypes)) {
                unset($type['value']);
            }
        }

        return $types;
    }

}
