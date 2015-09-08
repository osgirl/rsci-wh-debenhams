<?php

include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class picklist extends jdaCustomClass
{
	private static $formMsg = "";

	public static $user = 'SYS';
	public static $warehouseNo = "7000 ";
	/*

13
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
F1

	*/

// Note: BUG ON ERROR UPDATE SYNC STATUS, exceeds
	public function __construct() {
		// parent::__construct();
		parent::login();
	}

	private static function enterPickingMenu()
	{
		parent::$jda->screenWait("Picking Menu");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("19",22,44)),ENTER,true);
		echo "Entered: Picking Menu \n";
	}

	private static function enterApprovePicksIntoCartons()
	{
		parent::$jda->screenWait("Approve Picks into Cartons");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("23",22,44)),ENTER,true);
		echo "Entered: Approve Picks into Cartons \n";
	}

	public function enterForm($data)
	{
		print_r($data);
		parent::$jda->screenWait("Warehouse..");
		parent::display(parent::$jda->screen,132);

		$document_number = $data['document_number'];
		$store_number = $data['store_number'];
		$carton_code = $data['carton_code'];
		// $from_sequence = $data['from_sequence'];
		// $to_sequence = $data['to_sequence'];

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%5s", self::$warehouseNo), 5, 25); //enter location
		$formValues[] = array(sprintf("%6d", $document_number), 7, 25);// enter document no.
		// $formValues[] = array(sprintf("%5d", $from_sequence), 9, 25);// from sequence
		// $formValues[] = array(sprintf("%5d", $to_sequence), 9, 48);// to sequence
		$formValues[] = array(sprintf("%5d", $store_number), 11, 25);// enter store number
		// $formValues[] = array(sprintf("%9s", $carton_code), 13, 25);// enter carton id
		$formValues[] = array($carton_code, 13, 25);// enter carton id
		$formValues[] = array(self::$user, 15, 25);// enter warehouse clerk
		parent::$jda->write5250($formValues,F6,true);

		#special case when the completed date is less than todays date
		if(parent::$jda->screenCheck('The completion time cannot be before the assign time')) {
			parent::logError("The completion time cannot be before the assign time", __METHOD__);
			$date_now = date('n/d/y');
			$formValues[] = array(sprintf("%8s", $date_now), 16, 25);// enter date completed
			parent::$jda->write5250($formValues,F6,true);
		}

		parent::display(parent::$jda->screen,132);
		return self::checkResponse($data,__METHOD__);
	}

	private static function checkResponse($data,$source)
	{
		# error
		if(parent::$jda->screenCheck('Location entered is invalid')) {
            $receiver_message="Location entered is invalid";
			self::$formMsg = "{self::$warehouseNo}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data,"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Move document does not exist')) {
            $receiver_message="Move document does not exist";
			self::$formMsg = "{$data['document_number']}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data,"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('A valid store number is required')) {
            $receiver_message="A valid store number is required";
			self::$formMsg = "{$data['document_number']}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data['document_number'],"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('A carton id must be entered')) {
            $receiver_message="A carton id must be entered";
			self::$formMsg = "{$data['document_number']}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data['document_number'],"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Carton id entered is assigned to a different store')) {
            $receiver_message="Carton id entered is assigned to a different store";
			self::$formMsg = "{$data['document_number']}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data['document_number'],"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Store number entered is not valid')) {
            $receiver_message="Store number entered is not valid.";
			self::$formMsg = "{$data['store_number']}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data['document_number'],"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('The store requested is not assigned to the move selected')) {
            $receiver_message="The store requested is not assigned to the move selected";
			self::$formMsg = "{$data['document_number']}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data['document_number'],"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Warehouse clerk is invalid for this location')) {
            $receiver_message="Warehouse clerk is invalid for this location";
			self::$formMsg = "{self::$user}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data['document_number'],"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Sequence entry cannot be zero or negative')) {
            $receiver_message="Sequence entry cannot be zero or negative";
			self::$formMsg = "{$data['document_number']}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data['document_number'],"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		//if error persist exit
		if(parent::$jda->screenCheck('F5 to accept quantity greater than requested')) {
            $receiver_message="F5 to accept quantity greater than requested";
			self::$formMsg = "{$data['document_number']}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data['document_number'],"{$source}: {$receiver_message}", TRUE);
			parent::pressF1();
			parent::enterWarning();
			return false;
		}

		echo self::$formMsg;

		return true;
	}

	public function enterUpdateDetail()
	{
		parent::$jda->screenWait("Carton ID");
		parent::display(parent::$jda->screen,132);
		echo "Entered: Update Detail \n";
	}

	/*public function enterFormDetailsOrig($data)
	{
		$qtyMoved 	= self::getQtyMoved($data['move_doc_number']);
		parent::$jda->screenWait("SZ000001");

		$column 	= 9;
		$row 		= 37;
		$formValues = array();
		//coordinates start on 37/9
		for ($i=0; $i < count($qtyMoved); $i++) {
			$new_col = ($i + $column);
			echo "\n value of new_col is: {$new_col} \n";
			echo "value of quantity moved is: {$qtyMoved[$i]} \n";
			$formValues[] = array(sprintf("%10d", $qtyMoved[$i]),$new_col,$row); //enter moved_qty
		}
		print_r($formValues);
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250($formValues,F7,true);
		echo "Entered: Approve Picks Into Cartons Details \n";

		return self::checkResponse($data,__METHOD__);
	}*/

	public function enterFormDetails($data)
	{
		echo "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx \n";
		$qtyMoved 	= self::getQtyMoved($data['move_doc_number']);
		parent::$jda->screenWait("SZ000001");

		$column 	= 9;
		$row 		= 37;
		$limit      = 11;
		$total      = count($qtyMoved);
		$offset     = 0;
		$count      = ceil($total / $limit);
		// $formValues = array();
		//coordinates start on 37/9
		/*for ($i=0; $i < count($qtyMoved); $i++) {
			$new_col = ($i + $column);
			echo "\n value of new_col is: {$new_col} \n";
			echo "value of quantity moved is: {$qtyMoved[$i]} \n";
			$formValues[] = array(sprintf("%10d", $qtyMoved[$i]),$new_col,$row); //enter moved_qty
		}*/
		echo "\n Offset: {$offset} \n";
		echo "\n count: {$count} \n";
		while($offset < $count) {
			echo "\n Count: {$count} \n";
			$new = $offset;

			if ($new !== 0) {
				$new = $new * $limit;

				if (parent::$jda->screenWait("F6=Update Detail",5)) {
					parent::$jda->write5250(null,F6,true);
				}

				for($i=0; $i < $offset; $i++)
				{
					echo "\nCounter of i is: {$offset} \n";
					echo "\nEntered ROLLUP: Page: {$offset} with offset of: {$new} and row {$row} \n";
					parent::$jda->write5250(null,ROLLUP,true);
					parent::display(parent::$jda->screen,132);
				}
			}
			$page = array_slice( $qtyMoved, $new, $limit );
			$formValues = array();
			foreach ($page as $key => $value) {
				$new_column = $key + $column;
				echo "\n value of new_col is: {$new_column} \n";
				echo "value of qtyMoved is: {$value} \n";
				$formValues[] = array(sprintf("%10d", $value),$new_column,$row); //enter qty_delivered

			}
			parent::display(parent::$jda->screen,132);
			if(parent::$jda->screenWait("F7=Accept Qtys")){
				parent::$jda->write5250($formValues,F7,true);
			}
			else{
				echo "Unable to find F7=Accept Qtys";
			}
			$offset++;
		}
		echo "Entered: Approve Picks Into Cartons Details xxxxxxxxxxxxxxxxxxxxxxx\n";

		return self::checkResponse($data,__METHOD__);
	}

	/**
	 * Per sequence no entry
	 */
	public function enterFormDetailsPerSequence($data)
	{
		$qtyMoved 	= $data['moved_qty'];
		$formValues = array();

		parent::$jda->screenWait("SZ000001");

		$formValues[] = array(sprintf("%10d", $qtyMoved),9, 37); //enter moved_qty
		print_r($formValues);
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250($formValues,F7,true);
		echo "Entered: Approve Picks Into Cartons Details \n";

		return self::checkResponse($data,__METHOD__);
	}

	public function save($data)
	{
		if(parent::$jda->screenWait("F10=Accept Qty Assign Carton Markout Remaining"))
		{
			// parent::$jda->write5250(NULL,F7,true); // backup
			parent::$jda->write5250(NULL,F10,true); // TO CHECK
			parent::display(parent::$jda->screen,132);
			echo "Entered: Pressed F10 \n";


			#success
			if(parent::$jda->screenCheck("Document {$data['document_number']} accepted for store {$data['store_number']}") || parent::$jda->screenWait("Document {$data['document_number']} accepted for store {$data['store_number']}",5)) {
				self::$formMsg = "Document {$data['document_number']} accepted for store {$data['store_number']}";
				self::updateSyncStatus($data['document_number']);
				// parent::pressF1();
			}
			else{
				echo "Unable to find success message \n";
				parent::display(parent::$jda->screen,132);
			}
		}
		else{
			echo "Unable to find F10=Accept Qty Assign Carton Markout Remaining \n";
		}
	}

	/*
	* Get document number of picklist
	*/
	/*public function getPickNumber()
	{
		$db = new pdoConnection();

		echo "\n Getting move doc number from db \n";
		$sql = "SELECT DISTINCT pl.move_doc_number, b.store_code, MIN(bd.box_code) box_code
					FROM wms_box_details bd
					INNER JOIN wms_box b ON b.box_code = bd.box_code
					INNER JOIN wms_picklist_details pd ON bd.picklist_detail_id = pd.id
                    INNER JOIN wms_picklist pl ON pl.move_doc_number = pd.move_doc_number
					WHERE bd.sync_status = 0 AND pl_status = 2
					GROUP BY pl.move_doc_number
					ORDER BY pl.move_doc_number, sequence_no ASC";
					// GROUP BY picklist_detail_id
		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value;
		}

		$db->close();

		return $result;
	}*/

	/*
	* Get quantity moved per letdown document number
	*/
	private static function getQtyMoved($docNo)
	{
		$db = new pdoConnection();

		echo "\n Getting quantity delivered from db \n";
		/*$sql = "SELECT bd.id, pl.move_doc_number, b.store_code, picklist_detail_id, MIN(bd.box_code) box_code, sequence_no, pd.moved_qty
					FROM wms_box_details bd
					INNER JOIN wms_box b ON b.box_code = bd.box_code
					INNER JOIN wms_picklist_details pd ON bd.picklist_detail_id = pd.id
                    INNER JOIN wms_picklist pl ON pl.move_doc_number = pd.move_doc_number
					WHERE bd.sync_status = 0 AND pl_status = 2 AND pl.move_doc_number = {$docNo}
					GROUP BY picklist_detail_id
					ORDER BY pl.move_doc_number, sequence_no ASC";*/
		$sql = "SELECT moved_qty FROM wms_picklist_details WHERE move_doc_number = {$docNo}
				ORDER BY sequence_no ASC";
		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['moved_qty'];
		}

		$db->close();

		return $result;
	}

	/*
	* Update ewms box_details sync_status
	*/
	/*private static function updateSyncStatus($data, $isError = FALSE)
	{
		$db = new pdoConnection();
		$date_today = date('Y-m-d H:i:s');

		$status = ($isError) ? parent::$errorFlag : parent::$successFlag;
		print_r($data);
		echo "\n Getting receiver no from db \n";
		$sql 	= "UPDATE wms_box_details
					SET sync_status = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}'
					WHERE sync_status = 0 AND box_code = '{$data['carton_code']}'";
					// WHERE picklist_detail_id = {$data['picklist_detail_id']}
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";
		$db->close();
	}*/

	/*
	* Update ewms trasaction_to_jda sync_status
	*/
	private static function updateSyncStatus($reference,$error_message=null, $isError = FALSE)
	{
		$db = new pdoConnection();
		$date_today = date('Y-m-d H:i:s');

		$status = ($isError) ? parent::$errorFlag : parent::$successFlag;

		echo "\n Getting receiver no from db \n";
		$sql 	= "UPDATE wms_transactions_to_jda
					SET sync_status = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}', error_message = '{$error_message}'
					WHERE sync_status = 0 AND module = 'Picklist' AND jda_action='Closing' AND reference = {$reference}";
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";
		$db->close();
	}

	/*
	* On done only via android
	*/
	public function enterUpToApprovePicksIntoCartons()
	{
		try {
			$title = "Picklist \n";
			echo $title;
			parent::checkRecoverJob();
			parent::checkJobOnProgress();
			parent::enterDistributionManagement();
			self::enterPickingMenu();
			self::enterApprovePicksIntoCartons();


		} catch (Exception $e) {
			//send fail status
			echo 'Error: '. $e->getMessage();
		}

	}

	public function logout($params = array())
	{
		parent::logout();
		// self::syncBoxHeader($params);
	}

	private static function syncBoxHeader($params)
	{
		// $formattedString = "{$params['docNo']} {$params['boxNo']} {$params['palletNo']} {$params['loadNo']}";
		$formattedString = "{$params['loadNo']}";
		$dbInstance = new pdoConnection(); //open db connection
		$dbInstance->daemon('palletizing_step1', $formattedString);
		$dbInstance->close();

		echo "Entered: Syncing box header.... \n";
	}
}

$db 		= new pdoConnection(); //open db connection
$jdaParams 	= array();
$jdaParams 	= array('module' => 'Picklist', 'jda_action' => 'Closing');

// format: php picklist.php {docNo} {$boxNo} {$palletNo} {$loadNo}
$execParams 			= array();
$execParams['loadNo'] 	= ((isset($argv[1]))? $argv[1] : NULL);

if(isset($argv[1])) $jdaParams['reference'] = $execParams['loadNo'];
$document_nos = $db->getJdaTransactionPicklist($jdaParams);
if(! empty($document_nos) )
{
	$getPicklist = $db->getPicklistInfo($document_nos);

	print_r($getPicklist);
	$picklist = new picklist();
	$picklist->enterUpToApprovePicksIntoCartons();

	$params = array();
	foreach($getPicklist as $detail)
	{
		$params = array(
					'document_number' => $detail['move_doc_number'],
					'store_number'=> $detail['store_code'],
					'carton_code'=> $detail['box_code']
				);
		$validate = $picklist->enterForm($params);
		if($validate)
		{
			$picklist->enterUpdateDetail();
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