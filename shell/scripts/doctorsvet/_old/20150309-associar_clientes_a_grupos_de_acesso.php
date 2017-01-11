<?php

require_once '../../../app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

//Confirmar se todos os clientes estao em todos os grupos de acesso

$website_id = Mage::getModel('core/website')->load('doctorsvet')->getId();

$updated = array();

$collection = Mage::getModel('customer/customer')->getCollection()
	->addAttributeToSelect('id_erp')
	->addAttributeToFilter('website_id', $website_id)
;


foreach ($collection as $customer) {

	$updated[$customer->getIdErp()] = false;
}


$groups = array(
	'doctorsvet' 				=> array(1429,2782,1433,1435,1241,1242,2926,1437,2746,1443,1001,1239,1405,2736,1674,1247,1027,1249,2902,1495,1251,1680,1367,2806,1031,2704,2347,1813,2579,1717,1035,3013,1261,2991,2649,1045,1816,3019,2524,1500,2971,1269,1047,2312,2394,2804,3010,2877,3004,2210,1502,1286,1523,1292,2111,1290,1297,1299,1062,2848,1824,1306,1065,1096,1067,1310,1311,2185,2108,1653,3041,1148,2027,1314,2592,1074,2796,1658,1075,2981,1659,1080,1392,1661,2716,2883,2930,2764,2696,1663,1666,1668,2654,2676,2545,1116,1837,2441,1068,1321,2301,1672,1140,2961,1287,2932,1971,1145,2562,1153,2314,1677,1678,1326,1679,1681,2872,1977,2161,2191,2163,1685,1252,1686,2238,1676,1330,1174,2847,1687,1176,2984,2811,2107,1601,2889,2674,1179,1694,1282,2727,1272,1185,1338,1340,3007,1699,2965,2923,3046,2996,3044,1190,1191,2887,2462,2935,1702,1703,3006,2189,2919,2978,2933,2730,2974,3043,2739,2473,1293,1271,1270,1347,1348,1276,2998,1350,1351,1218,1144,2875,1352,2772,1219,3030,3036,1706,1274,1896,3052,1277,2465,1708,1220,1264,2660,1969,2559,2219,1473,1281,1285,1718,1240,1238,2893,1360,1719,1720,2366,1363,2959,1294,2482,1364,2976,1366,2864,3023,1309,1380,1724,1381,1725,2398,1369,1388,1730,1975,1691,1390,1733,2127,2931,2726,1734,2659,1394,1908,1375,1395,2972,2747,1376,2583,1704,1164,2999,2983,1712,1705,1379,1378,1377,1362,1358,1354,1341,1315,1304,1268,1243,1007),
	'doctorsvetexclusividade' 	=> array(1017,2478,1432,1118,2823,1527,1028,1036,2677,1528,1434,1530,2287,3034,1767,2869,2472,1532,2762,1094,1142,2774,2269,1852,1537,1538,2651,1987,2995,1066,1643,1564,2874,3040,1168,1797,1489,2895,3015,2845,1494,1499,1501,2501,1082,3056,2967,1504,1509,1650,1556,1199,1478,1546,1223,1547,2601,2781,2989,1561,1552,1198,2634,2741,2495,1100,1563,2249,1565,1101,1122,1570,1571,2460,1524,1106,2554,1583,1107,2616,1584,2859,1586,1593,1113,1204,1544,1600,2114,1208,1422,1913,1154,1652,1415,2840,3033,1591,2122,1161,1424,2178,1665,2405,1425,1602,2732,1743,2810,1211,1748,1750,2311,1427,2090,1431,1753,2729,1445,1614,1446,2851,2834,3028,2821,1757,2448,1449,1558,2436,1456,2745,1450,1452,1417,2997,2717,2988,2809,2288,2767,1470,1462,1460,1458,1457,1419,2763,1624,1114,1579,1764,2751,2776,2886,1627,2513,1577,1770,1771,3001,2656,1041,2567,2628,1472,1568,1474,2752,1455,1777,1475,1436,1779,2712,1545,2192,1477,2952,1781,2588,2701,2570,1480,1782,1788,1790,1791,1483,3032,1484,3045,1792,2794,1638,2940,1793,1796,1485,1010,2713,2924,1798,1505,1801,2870,1129,1805,2711,1510,1511,2880,1513,1515,1132,2121,1754,2419,3051,2957,2759,2409,1966,1765,1759,1615,1595,1569,1522,1520),
);

foreach ($groups as $store_code => $customers)
{
	$store_id = Mage::getModel('core/store')->load($store_code)->getId();

	$collection = Mage::getModel('customer/customer')->getCollection()
		->addAttributeToFilter('id_erp', array('in', $customers))
		->addAttributeToFilter('website_id', $website_id)
	;

	foreach ($collection as $customer) {
		$customer->setStoreView($store_id);
		$customer->getResource()->saveAttribute($customer, 'store_view');
		$updated[$customer->getIdErp()] = true;
	}
}

foreach ($updated as $key => $value)
{
	if (!$value)
		echo $key . "\n";
}