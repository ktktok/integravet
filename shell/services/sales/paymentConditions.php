<?php

require_once '/wwwroot/whitelabel/current/shell/services/configService.php';

array_shift($argv);
list($websiteId, $filePaymentConditions) = $argv;

if (empty($websiteId)) {
	die('You must provide a website id.');
}

if(empty($filePaymentConditions)) {
	die("You must provide a file");
}

if(!file_exists($filePaymentConditions)) {
	die("Arquivo não existe");
}

$lines = file($filePaymentConditions, FILE_IGNORE_NEW_LINES);

$fileArray = array();
$stores = Mage::getModel('core/website')->load($websiteId)->getStoreIds();
$total = 0;
if (empty($lines)) {
	echo "\n\n" . "arquivo vazio ou não existe" . "\n\n";
} else {
	foreach ($lines as $line) {
		$lineArray = explode('|', $line);
		$conditionIdErp = $lineArray[0];
		$conditionName = $lineArray[1];
		$conditionStartDay = $lineArray[2];
		$conditionQtySplit = $lineArray[3];
		$conditionDaysBetweenSplit = $lineArray[4];
		$conditionMinOrderValue = $lineArray[5];
		$conditionMaxOrderValue = $lineArray[6];
		$conditionDiscount = $lineArray[7];
		$conditionIncrease = $lineArray[8];
		$conditionAppliedToSpecifiedCustomers = $lineArray[9];
		$conditionCustomersIdsErp = $lineArray[10];

		if (conditionExists($conditionName, $stores)) {
			continue;
		}

		$condition = Mage::getModel('fvets_payment/condition');
		$condition->setData('id_erp', $conditionIdErp);
		$condition->setData('name', $conditionName);
		$condition->setData('start_days', $conditionStartDay);
		$condition->setData('split', $conditionQtySplit);
		$condition->setData('split_range', $conditionDaysBetweenSplit);
		$condition->setData('price_range_begin', $conditionMinOrderValue);
		$condition->setData('price_range_end', $conditionMaxOrderValue);
		$condition->setData('discount', $conditionDiscount);
		$condition->setData('increase', $conditionIncrease);
		$condition->setData('payment_methods', 'gwap_boleto');
		$condition->setData('apply_to_all', $conditionAppliedToSpecifiedCustomers);
		$condition->setData('status', '1');

		$arrayCustomers = array();
		foreach (explode(',', $conditionCustomersIdsErp) as $customerIdErp) {
			if(!$customerIdErp) {
				continue;
			}
			$customer = Mage::getModel('customer/customer')
				->getCollection()
				->addAttributeToFilter('website_id', $websiteId)
				->addAttributeToFilter('id_erp', $customerIdErp)
				->getFirstItem();
			if($customer->getId()) {
				$arrayCustomers[$customer->getId()] = array('position' => "");
			}
		}

		if($arrayCustomers) {
			$condition->setCustomersData($arrayCustomers);
		}
		$condition->setStores($stores);

		$condition->save();
		$total++;
	}

	echo "Foram cadastradas $total condições de pagamento";
}

function conditionExists($conditionName, $stores)
{
	$condition = Mage::getModel('fvets_payment/condition')
		->getCollection()
		->addStoreFilter($stores)
		->addFieldToFilter('name', $conditionName)
		->getFirstItem();

	if ($condition->getEntityId()) {
		return true;
	}
	return false;
}



