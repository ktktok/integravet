<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FVets
 * @package    FVets_CustomGrid
 * @copyright  Copyright (c) 2013 Benoît Leulliette <carlos.farah@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class FVets_CustomGrid_Block_Widget_Grid_Column_Renderer_Shipment_Items_Custom
    extends FVets_CustomGrid_Block_Widget_Grid_Column_Renderer_Sales_Items_Custom_Abstract
{
    protected function _getItemsBlockType()
    {
        return 'adminhtml/sales_order_shipment_view_items';
    }
    
    protected function _getActionLayoutHandle()
    {
        return 'adminhtml_sales_order_shipment_view';
    }
    
    protected function _getItemsBlockLayoutName()
    {
        return 'shipment_items';
    }
    
    protected function _getItemsBlockDefaultTemplate()
    {
        return 'sales/order/shipment/view/items.phtml';
    }
    
    protected function _prepareItemsBlock(Varien_Object $row)
    {
        Mage::unregister('current_shipment');
        Mage::register('current_shipment', $row);
        return $this;
    }
    
    protected function _getRowKey()
    {
        return 'shipment';
    }
}