<?php
require_once(__DIR__ . '/db_connection/db_purchase_order.php');
require_once(__DIR__ . '/keystroke/WHPurchaseOrder.php');
require_once(__DIR__ . '/keystroke/WHManual_Move.php');


$db_connect = new db_purchase_order_function();
$db_connect->connect();
$getClosedPO 	= $db_connect->getClosedPO();

foreach ($getClosedPO as $header) 
{
///////////////////////LOOP OF PO///////////////////////////////////


	///////////////////////Updating quantity///////////////////////////////////
	echo "Receiver no :".$header['receiver_no']."\n";
	$getPickedQty  = $db_connect->getQtyPO($header['receiver_no']);
	foreach ($getPickedQty as $details) 
	{
		echo "       SKU ->".$details['sku']."    ".$details['moved_qty']."\n";
		$db_connect->JDAUpdatePOQty($header['receiver_no'], $details['sku'],$details['moved_qty']);
	}
	///////////////////////Updating quantity///////////////////////////////////
		$getNotInPOQty   		= $db_connect->getNotInPO($header['purchase_order_no'], $header['receiver_no']);

		$WHPurchaseOrder = new WHPurchaseOrder (); 
		$WHPurchaseOrder->Login();
		if($WHPurchaseOrder)
		{ 
			$WHPurchaseOrder->Initiate(); 
			$WHPurchaseOrder->DoPurchaseOrder($header['receiver_no'],$header['invoice_no'],$header['po_status'], $getNotInPOQty); 
/*
			$WHManualMove = new WHManualMove ();
			$WHManualMove->Login();
			foreach ($getPickedQty as $keyvalue) 
			{ 
				if($WHManualMove)
				{ 
					$WHManualMove->Initiate(); 
					$WHManualMove->DoManualMove( $keyvalue['sku'],$keyvalue['slot_code'],$keyvalue['moved_qty']); 
				//$db_connect->updateIsSyncedShipping($header['load_code']);
				}
			} 
			$WHManualMove->LogOut();*/
		}
		//$db_connect->updateIsSyncedPO($header['receiver_no']);
///////////////////////LOOP OF PO///////////////////////////////////
}

