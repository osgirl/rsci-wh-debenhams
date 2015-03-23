<?php

chdir(dirname(__FILE__));
include_once('ewms_cron_class.php');

$ewms = new cronEWMS();
$ewms->purchaseOrder(); 		sleep(10);
$ewms->purchaseOrderDetails();	sleep(10);
$ewms->picklist();				sleep(10);
$ewms->picklistDetail();		sleep(10);
$ewms->storeOrder();			sleep(10);
$ewms->storeOrderDetails();		sleep(10);
$ewms->storeReturn();			sleep(10);
$ewms->storeReturnDetail();
$ewms->close();