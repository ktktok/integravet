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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

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

// NOTE : This file is currently no more up to date, what is likely to be fixed in a next release

class FVets_CustomGrid_Block_Grid_Rewrite
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
    * Custom grid model
    * 
    * @var FVets_CustomGrid_Model_Grid
    */
    private $_fvetscg_gridModel   = null;
    /**
    * Grid type model
    * 
    * @var FVets_CustomGrid_Model_Grid_Type_Abstract
    */
    private $_fvetscg_typeModel   = null;
    /**
    * Updated filters value
    * 
    * @var string
    */
    private $_fvetscg_filterParam = null;
    /**
    * Export informations
    * 
    * @var array
    */
    private $_fvetscg_exportInfos = null;
    /**
    * Collection to use instead of original one when exporting
    * 
    * @var Varien_Data_Collection
    */
    private $_fvetscg_exportedCollection    = null;
    /**
    * Flag telling whether collection preparation should be held
    * 
    * @var bool
    */
    private $_fvetscg_holdPrepareCollection = false;
    /**
    * Flag telling whether "prepare"-like events can be dispatched
    * 
    * @var bool
    */
    private $_fvetscg_prepareEventsEnabled  = true;
    /**
    * Additional attributes to select
    * 
    * @var array
    */
    private $_fvetscg_additionalAttributes  = array();
    /**
    * Flag telling whether additional attributes should be selected on next collection prepare
    * 
    * @var bool
    */
    private $_fvetscg_mustSelectAdditionalAttributes = false;
    
    /**
    * Set collection object
     *
     * @param Varien_Data_Collection $collection
     * @return mixed
    */
    public function setCollection($collection)
    {
        if (!is_null($this->_fvetscg_typeModel)) {
            $this->_fvetscg_typeModel->beforeGridSetCollection($this, $collection);
        }
        $return = parent::setCollection($collection);
        if (!is_null($this->_fvetscg_typeModel)) {
            $this->_fvetscg_typeModel->afterGridSetCollection($this, $collection);
        }
        return $return;
    }
    
    /**
     * Return collection object
     *
     * @return Varien_Data_Collection
     */
    public function getCollection()
    {
        $collection = parent::getCollection();
        if ($this->_fvetscg_mustSelectAdditionalAttributes
            && ($collection instanceof Mage_Eav_Model_Entity_Collection_Abstract)
            && count($this->_fvetscg_additionalAttributes)) {
            $this->_fvetscg_mustSelectAdditionalAttributes = false;
            foreach ($this->_fvetscg_additionalAttributes as $attribute) {
                $collection->joinAttribute(
                    $attribute['alias'],
                    $attribute['attribute'],
                    $attribute['bind'],
                    $attribute['filter'],
                    $attribute['join_type'],
                    $attribute['store_id']
                );
            }
        }
        return $collection;
    }
    
    /**
    * Set and apply filter values
    * 
    * @param array $data Filter values
    * @return this
    */
    protected function _setFilterValues($data)
    {
        if ($this->_fvetscg_holdPrepareCollection) {
            // Do not allow to set filters now, as we want to get the maximum of chances of getting items
            return $this;
        } else {
            if (!is_null($this->_fvetscg_gridModel)) {
                // Filter filters
                $data = $this->_fvetscg_gridModel->verifyGridBlockFilters($this, $data);
            }
            return parent::_setFilterValues($data);
        }
    }
    
    /**
     * Prepare grid collection object
     *
     * @return this
     */
    protected function _prepareCollection()
    {
        if (!is_null($this->_fvetscg_typeModel)) {
            $this->_fvetscg_typeModel->beforeGridPrepareCollection($this, $this->_fvetscg_prepareEventsEnabled);
        }
        if ($this->_fvetscg_prepareEventsEnabled) {
            Mage::getSingleton('customgrid/observer')->beforeGridPrepareCollection($this);
            $return = parent::_prepareCollection();
            Mage::getSingleton('customgrid/observer')->afterGridPrepareCollection($this);
        } else {
            $return = parent::_prepareCollection();
        }
        if (!is_null($this->_fvetscg_typeModel)) {
            $this->_fvetscg_typeModel->afterGridPrepareCollection($this, $this->_fvetscg_prepareEventsEnabled);
        }
        return $return;
    }
    
    /**
     * Iterate collection and call callback method per item
     * For callback method first argument always is item object
     *
     * @param string $callback
     * @param array $args additional arguments for callback method
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    
    public function _exportIterateCollection($callback, array $args)
    {
        if (!is_array($this->_fvetscg_exportInfos)) {
            return parent::_exportIterateCollection($callback, $args);
        } else {
            // Custom export
            if (!is_null($this->_fvetscg_exportedCollection)) {
                $originalCollection = $this->_fvetscg_exportedCollection;
            } else {
                $originalCollection = $this->getCollection();
            }
            if ($originalCollection->isLoaded()) {
                // Should do the trick in all exceptions (if not loaded page size can be changed)
                Mage::throwException(Mage::helper('customgrid')->__('This grid does not seem to be compatible with the custom export. If you wish to report this problem, please indicate this class name : "%s"', get_class($this)));
            }
            
            // 1000 up to 1.4, class var from 1.5
            $exportPageSize = (isset($this->_exportPageSize) ? $this->_exportPageSize : 1000);
            $infos = $this->_fvetscg_exportInfos;
            $total = (isset($infos['custom_size']) ?
                intval($infos['custom_size']) : 
                (isset($infos['size']) ? intval($infos['size']) : $exportPageSize));
            
            if ($total <= 0) {
                return;
            }
            
            $fromResult = (isset($infos['from_result']) ? intval($infos['from_result']) : 1);
            $pageSize   = min($total, $exportPageSize);
            $page       = ceil($fromResult/$pageSize);
            $pitchSize  = ($fromResult > 1 ? $fromResult-1 - ($page-1)*$pageSize : 0);
            $break      = false;
            $count      = null;
            
            while ($break !== true) {
                $collection = clone $originalCollection;
                $collection->setPageSize($pageSize);
                $collection->setCurPage($page);
                
                if (!is_null($this->_fvetscg_typeModel)) {
                    $this->_fvetscg_typeModel->beforeGridExportLoadCollection($this, $collection);
                }
                $collection->load();
                if (!is_null($this->_fvetscg_typeModel)) {
                    $this->_fvetscg_typeModel->afterGridExportLoadCollection($this, $collection);
                }
                
                if (is_null($count)) {
                    $count = $collection->getSize();
                    $total = min(max(0, $count-$fromResult+1), $total);
                    if ($total == 0) {
                        $break = true;
                        continue;
                    }
                    $first = true;
                    $exported = 0;
                }
                
                $page++;
                $i = 0;
                
                foreach ($collection as $item) {
                    if ($first) {
                        if ($i++ < $pitchSize) {
                            continue;
                        } else {
                            $first = false;
                        }
                    }
                    if (++$exported > $total) {
                        $break = true;
                        break;
                    }
                    call_user_func_array(array($this, $callback), array_merge(array($item), $args));
                }
            }
        }
    }
    
    /**
    * Add additional attribute to select
    * 
    * @param array $attribute Informations needed for attribute selection
    * @return this
    */
    public function fvetscg_addAdditionalAttribute(array $attribute)
    {
        $this->_fvetscg_additionalAttributes[] = $attribute;
        return $this;
    }
    
    /**
    * Set collection to use instead of original one when exporting
    * 
    * @param Varien_Data_Collection $collection
    * @return this
    */
    public function fvetscg_setExportedCollection($collection)
    {
        $this->_fvetscg_exportedCollection = $collection;
        return $this;
    }
    
    /**
    * Put collection prepares on hold
    * 
    * @return this
    */
    public function fvetscg_holdPrepareCollection()
    {
        $this->_fvetscg_holdPrepareCollection = true;
        return $this;
    }
    
    /**
    * Finish preparing collection 
    * (result in a complete call to _prepareCollection())
    * 
    * @return this
    */
    public function fvetscg_finishPrepareCollection()
    {
        if ($this->getCollection()) {
            $this->_fvetscg_holdPrepareCollection = false;
            $this->_fvetscg_prepareEventsEnabled  = false;
            // Prepare collection again, with now everything enabled
            // As columns are up-to-date, filters and orders should be successfully applied
            $this->_fvetscg_mustSelectAdditionalAttributes = true;
            $this->_prepareCollection();
        }
        return $this;
    }
    
    /**
    * Remove a column from the corresponding list
    * 
    * @param string $id Column ID
    * @return this
    */
    public function fvetscg_removeColumn($id)
    {
        if (array_key_exists($id, $this->_columns)) {
            unset($this->_columns[$id]);
            if ($this->_lastColumnId == $id) {
                // Recompute last column ID
                $this->_lastColumnId = array_pop(array_keys($this->_columns));
            }
        }
        return $this;
    }
    
    /**
    * Reset all columns orders
    * 
    * @return this
    */
    public function fvetscg_resetColumnsOrder()
    {
        $this->_columnsOrder = array();
        return $this;
    }
    
    /**
    * Common getters and setters
    */
    
    public function fvetscg_setGridModel($model)
    {
        $this->_fvetscg_gridModel = $model;
        return $this;
    }
    
    public function fvetscg_setTypeModel($model)
    {
        $this->_fvetscg_typeModel = $model;
        return $this;
    }
    
    public function fvetscg_setFilterParam($param)
    {
        $this->_fvetscg_filterParam = $param;
        return $this;
    }
    
    public function fvetscg_getFilterParam()
    {
        return $this->_fvetscg_filterParam;
    }
    
    public function fvetscg_setExportInfos($infos)
    {
        $this->_fvetscg_exportInfos = $infos;
    }
    
    public function fvetscg_getStore()
    {
        if (method_exists($this, '_getStore')) {
            return $this->_getStore();
        }
        $storeId = (int)$this->getRequest()
            ->getParam(Mage::helper('customgrid/config')->getStoreParameter('store'), 0);
        return Mage::app()->getStore($storeId);
    }
    
    public function fvetscg_getSaveParametersInSession()
    {
        return $this->_saveParametersInSession;
    }
    
    public function fvetscg_getSessionParamKey($name)
    {
        return $this->getId().$name;
    }
    
    public function fvetscg_getPage()
    {
        if ($this->getCollection() && $this->getCollection()->isLoaded()) {
            return $this->getCollection()->getCurPage();
        }
        return $this->getParam($this->getVarNamePage(), $this->_defaultPage);
    }
    
    public function fvetscg_getLimit()
    {
        return $this->getParam($this->getVarNameLimit(), $this->_defaultLimit);
    }
    
    public function fvetscg_getSort()
    {
        $columnId = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
        if (isset($this->_columns[$columnId]) && $this->_columns[$columnId]->getIndex()) {
            return $columnId;
        }
        return null;
    }
    
    public function fvetscg_getDir()
    {
        if ($this->fvetscg_getSort()) {
            return (strtolower($this->getParam($this->getVarNameDir(), $this->_defaultDir)) == 'desc') 
                ? 'desc' : 'asc';
        }
        return null;
    }
    
    public function fvetscg_getCollectionSize()
    {
        if ($this->getCollection()) {
            return $this->getCollection()->getSize();
        }
        return null;
    }
}