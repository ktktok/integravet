<?php

if(Mage::helper('core')->isModuleEnabled('Innoexts_Warehouse')){
	class MageFM_Customer_Model_Checkout_Type_Onepage_Tmp extends  Innoexts_Warehouse_Model_Checkout_Type_Onepage {}
} else {
	class MageFM_Customer_Model_Checkout_Type_Onepage_Tmp extends Mage_Checkout_Model_Type_Onepage {}
}

class MageFM_Customer_Model_Checkout_Type_Onepage extends MageFM_Customer_Model_Checkout_Type_Onepage_Tmp
{

	public function saveOrder()
	{
		Mage::dispatchEvent('checkout_type_onepage_save_order_before',
			array('quote' => $this->getQuote()));
		parent::saveOrder();
	}

    public function saveCustomer($data)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => Mage::helper('checkout')->__('Invalid data.'));
        }

        if (!$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
            if ($this->_customerEmailExists($data['email'], Mage::app()->getWebsite()->getId())) {
                return array('error' => 1, 'message' => Mage::helper('checkout')->__('There is already a customer registered using this email address. Please login using this email address or enter a different email address to register your account.'));
            }
        }

        $this->_validateCustomer($data);
        $this->getQuote()->save();

        $this->getCheckout()
                ->setStepData('customer', 'allow', true)
                ->setStepData('customer', 'complete', true)
                ->setStepData('billing', 'allow', true);

        return array();
    }

    protected function _validateCustomerData(array $data)
    {
        if ($this->getCheckoutMethod() == self::METHOD_GUEST || $this->getCheckoutMethod() == self::METHOD_REGISTER) {
            return true;
        }

        return $this->_validateCustomer($data);
    }

    protected function _validateCustomer(array $data)
    {
        return parent::_validateCustomerData($data);
    }

}