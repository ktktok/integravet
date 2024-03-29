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
 * @copyright  Copyright (c) 2014 Carlos Farah <carlos.farah@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class FVets_CustomGrid_Model_System_Config_Source_Default_Param_Behaviour_Scalar
{
    public function toOptionArray()
    {
        $helper = Mage::helper('customgrid');
        return array(
            array(
                'value' => FVets_CustomGrid_Model_Grid::GRID_DEFAULT_PARAM_DEFAULT,
                'label' => $helper->__('Default (Last Set Value is Used)'),
            ),
            array(
                'value' => FVets_CustomGrid_Model_Grid::GRID_DEFAULT_PARAM_FORCE_ORIGINAL,
                'label' => $helper->__('Force Original Value'),
            ),
            array(
                'value' => FVets_CustomGrid_Model_Grid::GRID_DEFAULT_PARAM_FORCE_CUSTOM,
                'label' => $helper->__('Force Custom Value'),
            ),
        );
    }
}
