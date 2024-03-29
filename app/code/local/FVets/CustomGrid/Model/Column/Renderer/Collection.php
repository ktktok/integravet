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

class FVets_CustomGrid_Model_Column_Renderer_Collection
    extends FVets_CustomGrid_Model_Column_Renderer
{   
    protected function _construct()
    {
        $this->_acceptParameters = true;
    }
    
    public function getConfigType()
    {
        return FVets_CustomGrid_Model_Config::TYPE_COLUMN_RENDERERS_COLLECTION;
    }
}