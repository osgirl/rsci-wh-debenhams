<?php

chdir(dirname(__FILE__));
include_once('db2_cron_class.php');

$db2 = new cronDB2();

$db2->purchaseOrder();		sleep(10);
$db2->purchaseOrderDetails();	sleep(10);
$db2->pickingDetail(); 			sleep(10);
$db2->picking();				sleep(10);
/*$db2->stores();					sleep(10);
$db2->storeOrder();			sleep(10);
$db2->storeOrderDetails();		sleep(10);
$db2->storeReturn(); 			sleep(10);
$db2->storeReturnDetails();*/
$db2->close();