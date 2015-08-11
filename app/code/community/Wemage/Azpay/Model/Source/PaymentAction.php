<?php


class Wemage_Azpay_Model_Source_PaymentAction
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Wemage_Azpay_Model_Cc::ACTION_AUTHORIZE,
                'label' => Mage::helper('azpay')->__('Authorize Only')
            ),
            array(
                'value' => Wemage_Azpay_Model_Cc::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('azpay')->__('Authorize and Capture')
            ),
        );
    }
}
