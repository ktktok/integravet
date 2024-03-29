<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * admin customer left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class FVets_ImportPromotions_Block_Adminhtml_System_Convert_Profile_Edit_Tabs extends Mage_Adminhtml_Block_System_Convert_Profile_Edit_Tabs
{

	protected function _beforeToHtml()
	{
		$new = !Mage::registry('current_convert_profile')->getId();

		$isUserAdmin = Mage::helper('fvets_importpromotions')->isUserAdministrator();

		if ($isUserAdmin) {
			$this->addTab('edit', array(
				'label' => Mage::helper('adminhtml')->__('Profile Actions XML'),
				'content' => $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_edit')->initForm()->toHtml(),
				'active' => true,
			));
		}

		if (!$new) {
			$this->addTab('run', array(
				'label'     => Mage::helper('adminhtml')->__('Run Profile'),
				'content'   => $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_run')->toHtml(),
			));

			if ($isUserAdmin)
			{
				$this->addTab('history', array(
					'label' => Mage::helper('adminhtml')->__('Profile History'),
					'content' => $this->getLayout()->createBlock('adminhtml/system_convert_profile_edit_tab_history')->toHtml(),
				));
			}
		}

		return call_user_func(array(get_parent_class(get_parent_class($this)), '_beforeToHtml'));
	}
}
