<?php

chdir(dirname(__FILE__));
include_once('db2_cron_class.php');

$db2 = new cronDB2();

$db2->purchaseOrder();			sleep(10);
$db2->purchaseOrderDetails();	sleep(10);
$db2->inventory();				sleep(10);
$db2->close();
