<?php

class FVets_SalesRule_Model_Label extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('fvets_salesrule/label');
	}
}
