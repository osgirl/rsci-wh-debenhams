<?php
require_once(__DIR__ . '/db_connection/db_return_warehouse.php');
require_once(__DIR__ . '/keystroke/return_warehouse.php');


$db_connect = new db_return_warehouse_function ();
$db_connect->Connect();

$getClosed = $db_connect->getClosedRW();

foreach ($getClosed as $header) 
{
   	echo "MTS Number :".$header['move_doc_number']."\n";
	$getPickedQty  = $db_connect->getQtyRW($header['move_doc_number']);
	foreach ($getPickedQty as $details) 
	{
		echo "       SKU ->".$details['sku']."    ".$details['moved_qty']."\n";
		$db_connect->JDAUpdateRWQty($header['move_doc_number'], $details['sku'],$details['moved_qty']);
	}

		$whpicking = new ReturnWarehouse (); 
		$whpicking->Login();
		if($whpicking)
		{ 
			$whpicking->Initiate(); 
			$whpicking->DoRWHReceiving($header['move_doc_number']); 

		}
		$db_connect->updateIsSyncedRW($header['move_doc_number']);
		
}


