<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Sales order history
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */

if(Mage::helper('core')->isModuleEnabled('FVets_Sales')){
	class Innoexts_Warehouse_Block_Sales_Order_History_Tmp extends  FVets_Sales_Block_Order_History {}
} else {
	class Innoexts_Warehouse_Block_Sales_Order_History_Tmp extends Mage_Sales_Block_Order_History {}
}

class Innoexts_Warehouse_Block_Sales_Order_History 
    extends Innoexts_Warehouse_Block_Sales_Order_History_Tmp
{
    /**
     * Get warehouse helper
     *
     * @return  Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->getOrders()->setFlag('appendStockIds');
    }
}