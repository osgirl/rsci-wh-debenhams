<?php

include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class picklist extends jdaCustomClass
{
	private static $formMsg = "";

	private static $lagging = false;

	public static $user = 'SYS';
	public static $warehouseNo = "7000 ";
	


	 

/*13
19
23
ENTER warehouse_no: 9005
TAB: Enter doc_no: 243
TAB: Enter From Seq_no: 1
TAB: Enter To Seq no: 1
Enter store_no: 20
TAB: Enter carton_id: TXT000001
TAB: Enter warehouse_clerk: SYS
F6 (if error about completion add +1 to date completed then F6 again)
PER ITEM: Enter quantity_moved (loop)
F7
F10
ENTER warehouse_no: 9005
TAB: Enter doc_no: 243
TAB: Enter From Seq_no: 2
TAB: Enter To Seq no: 3
Enter store_no: 20
TAB: Enter carton_id: TXT000002
TAB: Enter warehouse_clerk: SYS
F6 (if error about completion add +1 to date completed then F6 again)
PER ITEM: Enter quantity_moved (loop)
F7
F10
so on..
F1*/

	 
 
// Note: BUG ON ERROR UPDATE SYNC STATUS, exceeds
 
 	 	private static function enterPickingMenu()
	{
	 
	 
	 
	}


	 

 
	 
}

$db 		= new pdoConnection(); //open db connection
$jdaParams 	= array();
$jdaParams 	= array('module' => 'Picklist', 'jda_action' => 'Closing');

// format: php picklist.php {docNo} {$boxNo} {$palletNo} {$loadNo}
 
$document_nos = $db->getJdaTransactionPicklist($jdaParams);
if(! empty($document_nos) )
{
	$getPicklist = $db->getPicklistInfo($document_nos);

	print_r($getPicklist);
	$picklist = new picklist();
 
	$params = array();
	foreach($getPicklist as $detail)
	{
		$params = array(
					'document_number' => $detail['move_doc_number'],
					 
				);
	 
		if($validate)
		{
 			$validateDetail = $picklist->enterFormDetails($detail);
			if($validateDetail)
			{
				$picklist->save($params);
			}
		}
	}
	$picklist->logout($execParams);
}
else
{
	/*echo " \n No rows found!. Proceed to Box Header Creation\n";
	$formattedString = "{$execParams['loadNo']}";
	$db->daemon('palletizing_step1', $formattedString);*/
}

$db->close(); //close db connection