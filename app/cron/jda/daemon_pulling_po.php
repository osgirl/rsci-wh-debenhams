 
<?php

chdir(dirname(__FILE__));
include_once(__DIR__.'/../ewms_cron_class.php');
include_once('db2_cron_class.php');

$ewms 		= new cronEWMS();
$db2 		= new cronDB2();




 $po 		= $db2->purchaseOrder();
$ewms->purchaseOrder();		 sleep(2);

$poDetail 	= $db2->purchaseOrderDetails();
$ewms->purchaseOrderDetails();	sleep(2);

$picking 		=$db2->picking(); 
$ewms->picklist();		sleep(10); 

$pickingDetail 		=$db2->pickingDetail();
$ewms->picklistDetail();	sleep(10);


$mts					= $db2->storeReturn();
$ewms->storeReturn(); 			sleep(2);

$mtsdetail 				=$db2->storeReturnDetails();
$ewms->storeReturnDetail(); 	sleep(2);

$sublockpick 			= $db2->storeReturn_pick();		
$ewms->storeReturn_pick() ; sleep(10);


$sublockpickdetail		=$db2->storeReturnDetails_pick();		
$ewms->storeReturnDetail_pick(); sleep(10);


$sublocreverse			=$db2->storeReturn_return();		
$ewms->storeReturn_return(); sleep(10);

$sublocreversedetail 	=$db2->storeReturnDetails_return();	 
$ewms->storeReturnDetail_return(); sleep(10);	
 
	 
$db2->close();
$ewms->close();