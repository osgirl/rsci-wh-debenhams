<?php

chdir(dirname(__FILE__));
include_once(__DIR__.'/../ewms_cron_class.php');
include_once('db2_cron_class.php');

$ewms 		= new cronEWMS();
$db2 		= new cronDB2();


$po 		= $db2->purchaseOrder();
$ewms->purchaseOrder();			sleep(2);
$poDetail 	= $db2->purchaseOrderDetails();
$ewms->purchaseOrderDetails();	sleep(2);
$db2->close();
$ewms->close();