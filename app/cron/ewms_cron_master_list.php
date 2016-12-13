<?php

chdir(dirname(__FILE__));
include_once('ewms_cron_class.php');

$ewms = new cronEWMS();

/**$ewms->products();		sleep(10);
$ewms->department();			sleep(10);
$ewms->slots();			sleep(10);
$ewms->vendors();		sleep(10);**/
$ewms->stores();		sleep(10);
$ewms->close();