#!/usr/local/bin/php -q
<?php

chdir(dirname(__FILE__));
include_once('ewms_cron_class.php');
include_once('db2_cron_class.php');

$ewms 		= new cronEWMS();
$db2 		= new cronDB2();


$products 	= $db2->products();
$ewms->products(); 	sleep(2);
$slots 		= $db2->slots();		
$ewms->slots(); 	sleep(2);
$department = $db2->department();	
$ewms->department(); 	sleep(2);
$vendors 	= $db2->vendors();	
$ewms->vendors(); 	sleep(2);
$stores 	= $db2->stores();		
$ewms->stores(); 	sleep(2);


$db2->close();
$ewms->close();