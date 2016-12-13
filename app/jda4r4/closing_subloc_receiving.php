<?php
require_once(__DIR__ . '/db_connection/db_subloc_receiving.php');
require_once(__DIR__ . '/keystroke/subloc_receiving.php');


$db_connect = new db_subloc_receiving_function ();
$db_connect->Connect();

$getClosed = $db_connect->getClosedSublocReceive();

foreach ($getClosed as $header) 
{
   	echo "MTS Number :".$header['move_doc_number']."\n";
	$getPickedQty  = $db_connect->getQtySublocReceive($header['move_doc_number']);
	foreach ($getPickedQty as $details) 
	{
		echo "       SKU ->".$details['sku']."    ".$details['moved_qty']."\n";
		$db_connect->JDAUpdateSublocReceiveQty($header['move_doc_number'], $details['sku'],$details['moved_qty']);
	}

		$whpicking = new ReturnWarehouse (); 
		$whpicking->Login();
		if($whpicking)
		{ 
			$whpicking->Initiate(); 
			$whpicking->DoSublocReceiving($header['move_doc_number']); 

		}
		//$db_connect->updateIsSyncedSublocReceive($header['move_doc_number']);
		
}


