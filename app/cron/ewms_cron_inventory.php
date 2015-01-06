#!/usr/local/bin/php -q
<?php

chdir(dirname(__FILE__));
include_once('ewms_cron_class.php');
include_once('db2_cron_class.php');

$ewms 		= new cronEWMS();
$db2 		= new cronDB2();

$inventory 	= $db2->inventory();
$ewms->inventory();		sleep(2);

$db2->close();
$ewms->close();