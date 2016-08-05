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

    /**
     * Retrieve availables credit card types
     * Mage_Payment_Block_Form_Cc
     * @return array
     *
     */
    public function getCcAvailableTypes() {
        $types = Mage::getModel('azpay/source_cctypes')->toOptionArray();

        $allowedTypes = array();

        foreach ($types as $type) {
            $verifyType = Mage::getStoreConfig('payment/azpay_cc/acquirer_'.$type['value'].'');
            if ($verifyType != '0')
                $allowedTypes[] = $type;
        }

        return $allowedTypes;
    }

}
