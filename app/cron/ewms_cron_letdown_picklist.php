#!/usr/local/bin/php -q
<?php

chdir(dirname(__FILE__));
include_once('ewms_cron_class.php');
include_once('db2_cron_class.php');

$ewms 		= new cronEWMS();
$db2 		= new cronDB2();

$letdown 		= $db2->letdown();
$ewms->letdown();			sleep(2);
$letdownDetail 	= $db2->letdownDetail();
$ewms->letdownDetail();	sleep(2);
$picking 		= $db2->picking();
$ewms->picklist(); 			sleep(2);
$pickingDetail	= $db2->pickingDetail(); 		
$ewms->picklistDetail(); sleep(2);

$db2->close();
$ewms->close();