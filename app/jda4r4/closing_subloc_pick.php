<?php
require_once(__DIR__ . '/db_connection/db_subloc_pick.php');
require_once(__DIR__ . '/keystroke/subloc_pick.php');


 


$db_connect = new DB_sublock_pick_function();
$db_connect->Connect();

$getClosed = $db_connect->getClosedSublocPicking();

foreach ($getClosed as $header) 
{
   	echo "MTS Number :".$header['move_doc_number']."\n";
	$getPickedQty  = $db_connect->getQtySublocPicked($header['move_doc_number']);
	foreach ($getPickedQty as $details) 
	{
		echo "       SKU ->".$details['sku']."    ".$details['moved_qty']."\n";
		$db_connect->JDAUpdateSublocPickedQty($header['move_doc_number'], $details['sku'],$details['moved_qty']);
	}

		$sublocpick = new SubLocPicking (); 
		$sublocpick->Login();
		if($sublocpick)
		{ 
			$sublocpick->Initiate(); 
			$sublocpick->DoSubLocPicking($header['move_doc_number']); 

		}
		$db_connect->updateIsSyncedSubloc($header['move_doc_number']);
}