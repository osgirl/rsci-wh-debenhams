<?php
require_once(__DIR__ . '/db_connection/db_picking_query.php');
require_once(__DIR__ . '/keystroke/WHPicking.php');


$db_connect = new DB_Picking_Functions();
$db_connect->Connect();

$getClosed = $db_connect->getClosedPicking();

foreach ($getClosed as $header) 
{
   	echo "MTS Number :".$header['move_doc_number']."\n";
	$getPickedQty  = $db_connect->getQtyPicked($header['move_doc_number']);
	foreach ($getPickedQty as $details) 
	{
		echo "       SKU ->".$details['sku']."    ".$details['moved_qty']."\n";
		$db_connect->JDAUpdatePickedQty($header['move_doc_number'], $details['sku'],$details['moved_qty']);
	}

		$whpicking = new WHPicking (); 
		$whpicking->Login();
		if($whpicking)
		{ 
			$whpicking->Initiate(); 
			$whpicking->getDoPicking($header['move_doc_number']); 

			
		}

		$db_connect->updateIsSynced($header['move_doc_number']);
		
}


