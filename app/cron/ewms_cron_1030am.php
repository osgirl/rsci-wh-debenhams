#!/usr/local/bin/php -q
<?php

chdir(dirname(__FILE__));
include_once('ewms_cron_class.php');

$ewms = new cronEWMS();

$ewms->purchaseOrder();			sleep(10);
$ewms->purchaseOrderDetails();	sleep(10);
$ewms->inventory();				sleep(10);
$ewms->close();
