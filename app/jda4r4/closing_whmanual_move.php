<?php
require_once(__DIR__ . '/db_connection/db_manual_move_query.php');
require_once(__DIR__ . '/keystroke/WHManual_Move.php');

//$db_connect = new DB_ManualMove_Functions();
//$db_connect->connect();
//$getSKUQTY 	= $db_connect->getSKUQTY();

//foreach ($getSKUQTY as $sku) 
//{
 	
		//echo "       Transfer no. : ".$details['move_doc_number']."\n"; 
		$WHManualMove = new WHManualMove ();
		$WHManualMove->Login();
		if($WHManualMove)
		{ 
			$WHManualMove->Initiate(); 
			$WHManualMove->DoManualMove("1313754","SZ000001","1"); 
			//$db_connect->updateIsSyncedShipping($header['load_code']);
		}

		
//}

