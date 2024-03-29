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

class FVets_CustomGrid_Helper_Js extends Mage_Core_Helper_Abstract
{
    public function prepareHtmlForJsOutput($html, $canTrim=false)
    {
        $parts  = preg_split('#\r\n|\r[^\n]|\n#', ($canTrim ? trim($html) : $html));
        $result = '';
        $first  = true;
        
        foreach ($parts as $part) {
            $result .= ($first ? '' : "\r\n+ ") . '\'' 
                       . str_replace(array('\\', '\'', '/'), array('\\\\', '\\\'', '\\/'), ($canTrim ? trim($part) : $part))
                       . '\'';
            $first = false;
        }
        
        return ($result !== '' ? $result : '\'\'');
    }
}