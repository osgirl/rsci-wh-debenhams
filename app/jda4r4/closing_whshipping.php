<?php
require_once(__DIR__ . '/db_connection/DBwhshipping.php');
require_once(__DIR__ . '/keystroke/whshipping.php');


 

$db_connect = new DB_function_whshipping();
$db_connect->connect();
$getClosed 	= $db_connect->getLoadCodeShip();

foreach ($getClosed as $header) 
{
   	echo "Transfer No. : ".$header['Transfer_no']."\n";
   ///	$getLoadNumber = $db_connect->getShipping($header['load_code']);
  
		////echo "       Transfer no. : ".$details['move_doc_number']."\n"; 
		$WHShipping = new WHShipping ();
		$WHShipping->Login();
		if($WHShipping)
		{ 
			$WHShipping->Initiate(); 
			$WHShipping->DoWHShipping($header['Transfer_no']); 
			$db_connect->updateIsSyncedWHShipping($header['Transfer_no']);
		}
		
	

		
}

