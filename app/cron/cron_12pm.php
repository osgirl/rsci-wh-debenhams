<?php

chdir(dirname(__FILE__));
include_once('db2_cron_class.php');

$db2 = new cronDB2();

/*$db2->storeOrder();				sleep(10);
$db2->storeOrderDetails();		sleep(10);
$db2->letdown();				sleep(10);
$db2->letdownDetail();			sleep(10);*/
$db2->picking();				sleep(10);
$db2->pickingDetail(); 			sleep(10);
$db2->close();
