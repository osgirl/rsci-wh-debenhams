<?php
require_once(__DIR__ . '/db_connection/db_subloc_shipping.php');
require_once(__DIR__ . '/keystroke/subloc_shipping.php');


 

$db_connect = new DB_function_subloc_shipping();
$db_connect->connect();
$getClosed 	= $db_connect->getLoadCodeShipSubloc();

foreach ($getClosed as $header) 
{
   	/*echo " Pell number :".$header['load_code']."\n";
   	$getLoadNumber = $db_connect->getShippingSubloc($header['load_code']);
 	foreach ($getLoadNumber as $details) 
	{*/
		echo "       Transfer no. : ".$header['move_doc_number']."\n"; 
		$SubLocShipping = new SubLocShipping ();
		$SubLocShipping->Login();
		if($SubLocShipping)
		{ 
			$SubLocShipping->Initiate(); 
			$SubLocShipping->DoSubLocShipping($header['move_doc_number']); 
			$db_connect->updateIsSyncedShipping($header['move_doc_number']);
		}
		
	/*}*/

		
}

