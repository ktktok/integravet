<?php

class FVets_Salesrep_Block_Product_List_Selector extends Mage_Page_Block_Html
{
	function getRegionCollection()
	{
		return Mage::getModel('directory/region')->getCollection()
			->addCountryFilter('BR');
	}
}