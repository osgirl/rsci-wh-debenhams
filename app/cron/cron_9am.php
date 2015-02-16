<?php

chdir(dirname(__FILE__));
include_once('db2_cron_class.php');

$db2 = new cronDB2();

$db2->products();	sleep(10);
$db2->slots();		sleep(10);
$db2->department();	sleep(10);
$db2->vendors();	sleep(10);
$db2->stores();		sleep(10);
$db2->close();