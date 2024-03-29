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
 
class FVets_CustomGrid_Model_Custom_Column_Shipment_Items_Custom
    extends FVets_CustomGrid_Model_Custom_Column_Shipment_Items_Abstract
{
    protected function _isCustomizableList()
    {
        return true;
    }
    
    public function getItemValues()
    {
        if (!$this->hasData('item_values')) {
            $baseValues = array(
                'name'     => 'Name',
                'sku'      => 'SKU',
                'quantity' => 'Qty',
            );
            $this->setData('item_values', $this->_getItemValuesList($baseValues, array(), 'fvetscg_custom_column_shipment_items_custom_values'));
        }
        return $this->_getData('item_values');
    }
    
    protected function _getGridColumnRenderer()
    {
        return 'customgrid/widget_grid_column_renderer_shipment_items_custom';
    }
}