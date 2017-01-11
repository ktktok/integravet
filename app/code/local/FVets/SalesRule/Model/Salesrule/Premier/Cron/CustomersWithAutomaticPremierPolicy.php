<?php

class FVets_SalesRule_Model_Salesrule_Premier_Cron_CustomersWithAutomaticPremierPolicy
{

	private $report = '';

	public function run()
	{
		$rustart = getrusage();

		//Buscar todos os clientes do site atual
		$customers = Mage::getModel('customer/customer')->getCollection()
			->addAttributeToSelect('id_erp')
			->addAttributeToSelect('firstname')
			->addAttributeToSelect('website_id')
			->addAttributeToSelect('group_id')
			->addAttributeToSelect('store_id')
			->addAttributeToFilter('automatic_premier_policy', '1')
		;

		foreach($customers as $customer)
		{
			Mage::app()->setCurrentStore($customer->getStoreId());
			if (Mage::getStoreConfig('fvets_salesrule/premier/status', $customer->getStoreId()))
			{
				//Excluir a relação com o cliente de todas as promoções do tipo premier
				$resource = Mage::getResourceSingleton('fvets_salesrule/salesrule_customer');
				$resource->deletePremierCustomerRelation($customer);

				//Buscar o último pedido do cliente para saber o último mês que o cliente fez um pedido
				$collection = Mage::getModel('fvets_sales/offline_order')
					->getCollection()
					->addFieldToSelect('created_at')
					->addFieldToFilter('website_id', $customer->getWebsiteId())
					->addFieldToFilter('customer_id_erp', $customer->getIdErp());
				$collection
					->getSelect()
					->order('created_at DESC');

				$lastOrder = $collection->getFirstItem();

				if ($lastOrder->getCreatedAt())
				{
					$createdAtTime = strtotime($lastOrder->getCreatedAt());
					//Buscar todos os pedidos do mesmo mês do último pedido do cliente
					$orders = Mage::getModel('fvets_sales/offline_order')
						->getCollection()
						->addFieldToFilter('website_id', $customer->getWebsiteId())
						->addFieldToFilter('customer_id_erp', $customer->getIdErp())
						->addFieldToFilter('created_at', array(array('lt' => date('Y', $createdAtTime) . '-' . ((int)date('m', $createdAtTime) + 1) . '-01 00:00:00')))
						->addFieldToFilter('created_at', array(array('gteq' => date('Y-m-d H:i:s', strtotime(date('Y-m', $createdAtTime) . '-01 00:00:00')))))
					;

					$weight = 0;
					if ($orders->getSize() > 0)
					{
						foreach ($orders as $order)
						{
							//Mage::app()->setCurrentStore($order->getStore());
							$search_category = Mage::getStoreConfig('fvets_salesrule/premier/category');
							//Buscar somente produtos da premier
							foreach($order->getAllItems() as $item)
							{
								$product = $item->getProduct();
								$product_categories = $product->getCategoryIds();
								if (in_array($search_category, $product_categories))
								{
									$weight += $product->getWeight() * $item->getQty();
								}
							}
						}
					}

					if ($weight > 0)
					{
						$this->validatePromo($weight, $customer);
					}
				}
			}
		}


		$ru = getrusage();
		$this->report .= "This process used " . $this->rutime($ru, $rustart, "utime") .
			" ms for its computations\n";
		$this->report .= "It spent " . $this->rutime($ru, $rustart, "stime") .
			" ms in system calls\n";

		$channel = 'test';
		Mage::helper('datavalidate')->createChannel($channel);
		Mage::helper('datavalidate')->sendSlackMessage($channel, $this->report);

		echo $this->report;
	}

	function validatePromo($weight, $customer)
	{
		//Buscar as promoções da premier com o mesmo website do cliente
		$collection = Mage::getModel('salesrule/rule')
			->getCollection()
			->addFieldToFilter('rule_type', 2)
			->addWebsiteFilter($customer->getWebsiteId())
		;
		$collection->getSelect()
			->joinInner(
				array('premier' => $collection->getTable('fvets_salesrule/premier')),
				'premier.salesrule_id=main_table.rule_id'
			)
			->where('premier.from <= ?', $weight)
			->where('premier.to > ?', $weight)
			->order('main_table.sort_order ASC')
			->group('premier.group')
		;

		//Adiciona o cliente na promoção com maior prioridade encontrada (a menor prioridade e a maior prioridade)
		foreach ($collection as $rule)
		{
			$resource = Mage::getResourceSingleton('fvets_salesrule/salesrule_customer');

			$data = array(
				$customer->getId() => array('position' => 0)
			);

			$salesruleCustomer = Mage::getResourceSingleton('fvets_salesrule/salesrule_customer')
				->savePremierSalesruleRelation($rule, $data);

			$this->report .= '"' . implode('","', array($customer->getId(), $customer->getIdErp(), $customer->getFirstname() . ' ' . $customer->getLastname(), $rule->getId(), $rule->getName(), $rule->getDescription())) . '"' . "\n";
		}
	}

	function rutime($ru, $rus, $index) {
		return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
		-  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
	}
}