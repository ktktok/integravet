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

class FVets_CustomGrid_Model_System_Config_Source_Yesno
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('customgrid')->__('Yes')),
            array('value' => 0, 'label' => Mage::helper('customgrid')->__('No')),
        );
    }
}