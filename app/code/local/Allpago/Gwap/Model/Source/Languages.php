<?php

/**
 * Allpago - Gwap Payment Module
 *
 * @title      Magento -> Custom Payment Module for Gwap
 * @category   Payment Gateway
 * @package    Allpago_Gwap
 * @author     Allpago Development Team
 * @copyright  Copyright (c) 2013 Allpago
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Allpago_Gwap_Model_Source_Languages {

    public function toOptionArray() 
    {
        return array(
            array('value'=>'1', 'label'=>Mage::helper('gwap')->__('Português')),
            array('value'=>'2', 'label'=>Mage::helper('gwap')->__('Inglês')),
            array('value'=>'3', 'label'=>Mage::helper('gwap')->__('Espanhol'))            
        );
    }

}