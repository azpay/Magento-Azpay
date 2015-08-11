<?php


class Wemage_Azpay_Model_Quote_Address_Interest extends Mage_Sales_Model_Quote_Address_Total_Abstract

{
    /**
     * Constructor that should initiaze
     */
    public function __construct()
    {
        $this->setCode('interest');
    }

    /**
     * Used each time when collectTotals is invoked
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Your_Module_Model_Total_Custom
     */

	public function collect(Mage_Sales_Model_Quote_Address $address)
	{
		if ($address->getData('address_type') == 'billing') return $this;

		$this->_setAddress($address);


		if($ammount = $address->getQuote()->getInterest())
		{
			$this->_setBaseAmount($ammount);
			$this->_setAmount($address->getQuote()->getStore()->convertPrice($ammount, false));
			$address->setInterest($ammount);
		}
		else
		{
			$this->_setBaseAmount(0.00);
			$this->_setAmount(0.00);
		}

		return $this;
	}

    /**
     * Used each time when totals are displayed
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Your_Module_Model_Total_Custom
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if ($address->getInterest() != 0)
        {
            $address->addTotal(array
            (
                'code' => $this->getCode(),
                'title' => Mage::helper('azpay')->__('Interest'),
                'value' => $address->getInterest()
            ));
        }
    }
}
