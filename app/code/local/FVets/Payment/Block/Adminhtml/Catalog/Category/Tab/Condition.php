<?php
/**
 * FVets_Payment extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Payment
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Condition tab on category edit form
 *
 * @category    FVets
 * @package     FVets_Payment
 * @author      Douglas Borella Ianitsky
 */
class FVets_Payment_Block_Adminhtml_Catalog_Category_Tab_Condition extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * constructor
     *
     * @access public
     * @author Douglas Borella Ianitsky
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('catalog_category_condition');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        if ($this->getCategory()->getId()) {
            $this->setDefaultFilter(array('in_conditions'=>1));
        }
    }

    /**
     * get current category
     *
     * @access public
     * @return Mage_Catalog_Model_Category|null
     * @author Douglas Borella Ianitsky
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * prepare the collection
     *
     * @access protected
     * @return FVets_Payment_Block_Adminhtml_Catalog_Category_Tab_Condition
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('fvets_payment/condition_collection');
        if ($this->getCategory()->getId()) {
            $constraint = 'related.category_id='.$this->getCategory()->getId();
        } else {
            $constraint = 'related.category_id=0';
        }
        $collection->getSelect()->joinLeft(
            array('related' => $collection->getTable('fvets_payment/condition_category')),
            'related.condition_id=main_table.entity_id AND '.$constraint,
            array('position')
        );
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * Prepare the columns
     *
     * @access protected
     * @return FVets_Payment_Block_Adminhtml_Catalog_Category_Tab_Condition
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_conditions',
            array(
                'header_css_class'  => 'a-center',
                'type'   => 'checkbox',
                'name'   => 'in_conditions',
                'values' => $this->_getSelectedConditions(),
                'align'  => 'center',
                'index'  => 'entity_id'
            )
        );
        $this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('fvets_payment')->__('Id'),
                'type'   => 'number',
                'align'  => 'left',
                'index'  => 'entity_id',
            )
        );
        $this->addColumn(
            'name',
            array(
                'header' => Mage::helper('fvets_payment')->__('Name'),
                'align'  => 'left',
                'index'  => 'name',
                'renderer' => 'fvets_payment/adminhtml_helper_column_renderer_relation',
                'params' => array(
                    'id' => 'getId'
                ),
                'base_link' => 'adminhtml/payment_condition/edit',
            )
        );
        $this->addColumn(
            'position',
            array(
                'header'         => Mage::helper('fvets_payment')->__('Position'),
                'name'           => 'position',
                'width'          => 60,
                'type'           => 'number',
                'validate_class' => 'validate-number',
                'index'          => 'position',
                'editable'       => true,
            )
        );
        return parent::_prepareColumns();
    }

    /**
     * Retrieve selected conditions
     *
     * @access protected
     * @return array
     * @author Douglas Borella Ianitsky
     */
    protected function _getSelectedConditions()
    {
        $conditions = $this->getCategoryConditions();
        if (!is_array($conditions)) {
            $conditions = array_keys($this->getSelectedConditions());
        }
        return $conditions;
    }

    /**
     * Retrieve selected conditions
     *
     * @access protected
     * @return array
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedConditions()
    {
        $conditions = array();
        //used helper here in order not to override the category model
        $selected = Mage::helper('fvets_payment/category')->getSelectedConditions(Mage::registry('current_category'));
        if (!is_array($selected)) {
            $selected = array();
        }
        foreach ($selected as $condition) {
            $conditions[$condition->getId()] = array('position' => $condition->getPosition());
        }
        return $conditions;
    }

    /**
     * get row url
     *
     * @access public
     * @param FVets_Payment_Model_Condition
     * @return string
     * @author Douglas Borella Ianitsky
     */
    public function getRowUrl($item)
    {
        return '#';
    }

    /**
     * get grid url
     *
     * @access public
     * @return string
     * @author Douglas Borella Ianitsky
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'adminhtml/payment_condition_catalog_category/conditionsgrid',
            array(
                'id'=>$this->getCategory()->getId()
            )
        );
    }

    /**
     * Add filter
     *
     * @access protected
     * @param object $column
     * @return FVets_Payment_Block_Adminhtml_Catalog_Category_Tab_Condition
     * @author Douglas Borella Ianitsky
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_conditions') {
            $conditionIds = $this->_getSelectedConditions();
            if (empty($conditionIds)) {
                $conditionIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$conditionIds));
            } else {
                if ($conditionIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$conditionIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
}
