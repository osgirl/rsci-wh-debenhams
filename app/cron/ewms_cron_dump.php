<?php

// include_once("ewms_connection.php");
chdir(dirname(__FILE__));
include_once('ewms_cron_class.php');


$ewms = new cronEWMS();

$ewms->products();
sleep(10);
$ewms->department();
sleep(10);
$ewms->slots();
sleep(10);
$ewms->vendors();
sleep(10);
$ewms->stores();
sleep(10);
$ewms->purchaseOrder();
sleep(10);
$ewms->purchaseOrderDetails();
sleep(10);
$ewms->inventory();
sleep(10);
$ewms->storeOrder();
sleep(10);
$ewms->storeOrderDetails();
sleep(10);
$ewms->letdown();
sleep(10);
$ewms->letdownDetail();
sleep(10);
$ewms->picklist();
sleep(10);
$ewms->picklistDetail();
$ewms->close();