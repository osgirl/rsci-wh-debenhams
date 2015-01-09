<?php

chdir(dirname(__FILE__));
include_once('ewms_cron_class.php');

$ewms = new cronEWMS();

$ewms->letdown();			sleep(10);
$ewms->letdownDetail();		sleep(10);
$ewms->picklist(); 			sleep(10);
$ewms->picklistDetail(); 	sleep(10);
$ewms->storeOrder();		sleep(10);
$ewms->storeOrderDetails();	sleep(10);
$ewms->close();