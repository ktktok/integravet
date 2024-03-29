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
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class FVets_Catalog_Block_Adminhtml_Catalog_Product_Grid extends Mage_Adminhtml_Block_Catalog_Product_Grid
{

	protected function _prepareCollection()
	{
		$store = $this->_getStore();
		$collection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('sku')
			->addAttributeToSelect('name')
			->addAttributeToSelect('attribute_set_id')
			->addAttributeToSelect('type_id')
			->addAttributeToSelect('id_erp');

		if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory'))
		{
			$collection->joinField('qty',
				'cataloginventory/stock_item',
				'qty',
				'product_id=entity_id',
				'{{table}}.stock_id=1',
				'left');
		}
		if ($store->getId())
		{
			//$collection->setStoreId($store->getId());
			$adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
			$collection->addStoreFilter($store);
			$collection->joinAttribute(
				'name',
				'catalog_product/name',
				'entity_id',
				null,
				'inner',
				$adminStore
			);
			$collection->joinAttribute(
				'custom_name',
				'catalog_product/name',
				'entity_id',
				null,
				'inner',
				$store->getId()
			);
			$collection->joinAttribute(
				'status',
				'catalog_product/status',
				'entity_id',
				null,
				'inner',
				$store->getId()
			);
			$collection->joinAttribute(
				'visibility',
				'catalog_product/visibility',
				'entity_id',
				null,
				'inner',
				$store->getId()
			);
			$collection->joinAttribute(
				'price',
				'catalog_product/price',
				'entity_id',
				null,
				'left',
				$store->getId()
			);
			$collection->joinAttribute(
				'id_erp',
				'catalog_product/id_erp',
				'entity_id',
				null,
				'left',
				$store->getId()
			);

		} else
		{
			$collection->addAttributeToSelect('price');
			$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
			$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
		}
		$this->setCollection($collection);
		call_user_func(array(get_parent_class(get_parent_class($this)), '_prepareCollection'));
		$this->getCollection()->addWebsiteNamesToResult();
		return $this;
	}

	protected function _prepareColumns()
	{

		$this->addColumn('id_erp',
			array(
				'header' => Mage::helper('catalog')->__('ID ERP'),
				'width' => '80px',
				'index' => 'id_erp',
			));

		return parent::_prepareColumns();
	}
}
