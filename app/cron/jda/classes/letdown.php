<?php

include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class letdown extends jdaCustomClass 
{	
	private static $formMsg = "";

	public static $user = 'SYS';
	public static $warehouseNo = "9005 ";
	public static $fromSlot = "RZ000001";
	/*

13
18
19
enter location: 9005
TAB: Enter document_number -> 246
TAB: Enter warehoue clerk -> SYS
F6
PER ITEM: Enter quantity_moved (loop)
F7
F7 (return to enter/approve letdown qunatites screen) //if(success) Document {doc_no} accepted
F10 (submit and return)
ENTER


The completion time cannot be before the assign time
if error +1 in date completed

NOTE: per sequence no ASCENDING order


	*/

	public function __construct() {
		// parent::__construct();
		parent::login();
	}

	private static function enterLetdownMenu()
	{
		parent::$jda->screenWait("Letdown Menu");
		parent::display(parent::$jda->screen,132);	
		parent::$jda->write5250(array(array("18",22,44)),ENTER,true);
		echo "Entered: Letdown Menu \n";
	}

	public function enterEnterApproveLetdowns()
	{
		parent::$jda->screenWait("Enter and Approve Letdowns");
		parent::display(parent::$jda->screen,132);	
		parent::$jda->write5250(array(array("19",22,44)),ENTER,true);
		echo "Entered: Enter and Approve Letdowns \n";
	}

	public function enterApprovedLetdownQuantitiesForm()
	{
		parent::$jda->screenWait("Date completed");
		parent::display(parent::$jda->screen,132);	
	}

	public function enterForm($data)
	{
		$document_number = $data['document_number'];
		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%5s", self::$warehouseNo), 5, 25); //enter location
		$formValues[] = array(sprintf("%6d", $document_number), 9, 25);// enter document no.
		$formValues[] = array(self::$user, 11, 25);// enter warehouse clerk
		parent::$jda->write5250($formValues,F6,true);
		
		#special case when the completed date is less than todays date
		if(parent::$jda->screenCheck('The completion time cannot be before the assign time')) {
			parent::logError("The completion time cannot be before the assign time", __METHOD__);
			$date_now = date('n/d/y');
			$formValues[] = array(sprintf("%8s", $date_now), 16, 25);// enter date completed
			parent::$jda->write5250($formValues,F6,true);
		}
		return self::checkResponse($data,__METHOD__);
	}

	private static function checkResponse($data,$source)
	{
		# error
		if(parent::$jda->screenCheck('Location entered is invalid')) {
            $receiver_message='Location entered is invalid';
			self::$formMsg = "{self::$warehouseNo}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data,"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Move document does not exist')) {
            $receiver_message='Move document does not exist';
			self::$formMsg = "{$data['document_number']}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data,"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Status code of move transaction is not "open"')) {
            $receiver_message="Status code of move transaction is not 'open'";
			self::$formMsg = "{$data['document_number']}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data,"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Warehouse clerk is invalid for this location')) {
            $receiver_message="Warehouse clerk is invalid for this location";
			self::$formMsg = "{self::$user}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data,"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('quantity greater than requested')) {
            $receiver_message="F5 to accept quantity greater than requested";
			self::$formMsg = "{$data['document_number']}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data,"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Use of decimals incorrect or too many numbers entered')) {
            $receiver_message="Use of decimals incorrect or too many numbers entered";
			self::$formMsg = "{$data['document_number']}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data,"{$source}: {$receiver_message}", TRUE);
			return false;
		}
		#end error

		#success
		if(parent::$jda->screenCheck("Document {$data['document_number']} accepted.")) {
			self::$formMsg = "Document {$data['document_number']} accepted";
			self::enterSubmit();
		}

		echo self::$formMsg;

		return true;
	}

	public function enterUpdateDetail() 
	{
		parent::$jda->screenWait("Start at Seq No");
		parent::display(parent::$jda->screen,132);	
		echo "Entered: Update Detail \n";
	}

    public function enterFormDetails($data) 
	{
		$qtyMoved 	= self::getQtyMoved($data['document_number']);
		$column 	= 9;
		$row 		= 37;
		// $qtyMoved 	= array(1);
		$formValues = array();
		//coordinates start on 37/9
		for ($i=0; $i < count($qtyMoved); $i++) {
			$new_col = ($i + $column);
			echo "\n value of new_col is: {$new_col} \n";
			echo "value of quantity moved is: {$qtyMoved[$i]} \n";
			$formValues[] = array(sprintf("%10d", $qtyMoved[$i]),$new_col,$row); //enter moved_qty
		}
		
		parent::$jda->write5250($formValues,F7,true);
		echo "Entered: Enter and Approve Letdowns Details \n";

		return self::checkResponse($data,__METHOD__);
	}

	public function submit($data)
	{
		self::enterUpdateDetailAgain($data);
		self::enterJobQueue($data);
	}

	private static function enterUpdateDetailAgain($data) 
	{
		parent::$jda->screenWait("CENTRAL WAREHOUSE");
		parent::display(parent::$jda->screen,132);	
		parent::$jda->write5250(NULL,F7,true);
		echo "Entered: Update Detail Again \n";

		self::checkResponse($data,__METHOD__);
	}

	private static function enterSubmit() 
	{
		parent::$jda->screenWait("Enter Submit and Return");
		parent::display(parent::$jda->screen,132);	
		parent::$jda->write5250(NULL,F10,true);
		echo "Entered: Submit and Return \n";
	}

	private static function enterJobQueue($data) 
	{
		parent::$jda->screenWait("Submitted Job Name");
		parent::display(parent::$jda->screen,132);	
		parent::$jda->write5250(NULL,ENTER,true);
		echo "Entered: Job Queue \n";
		parent::display(parent::$jda->screen,132);	

		self::updateSyncStatus($data['document_number']);
	}

	/*public function getDocumentNo() 
	{
		$db = new pdoConnection();

		echo "\n Getting receiver no from db \n";
		$sql 	= "SELECT reference 
					FROM wms_transactions_to_jda 
					WHERE module = 'Letdown' AND jda_action='Closing' AND sync_status = 0";
		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['reference'];
		}

		$db->close();

		return $result;
	}*/

	/*
	* Get quantity moved per letdown document number
	*/
	private static function getQtyMoved($document_number) 
	{
		$db = new pdoConnection();

		echo "\n Getting quantity delivered from db \n";
		$sql 	= "SELECT moved_qty FROM wms_letdown_details WHERE move_doc_number = {$document_number}
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
					WHERE sync_status = 0 AND module = 'Letdown' AND jda_action='Closing' AND reference = {$reference}";
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";
		$db->close();
	}


	/*
	* On done only via android
	*/
	public function enterUpToLetdownMenu()
	{	
		try {
			$title = "Letdown \n";
			echo $title;
			parent::checkRecoverJob();
			parent::checkJobOnProgress();
			parent::enterDistributionManagement();
			self::enterLetdownMenu();
			
		} catch (Exception $e) {
			//send fail status
			echo 'Error: '. $e->getMessage();
		}
		
	}

	public function logout()
	{	
		parent::logout();
	}
}

$db = new pdoConnection(); //open db connection

$jdaParams = array();
$jdaParams = array('module' => 'Letdown', 'jda_action' => 'Closing');
if($argv[1]) $jdaParams['reference'] = $argv[1];// for manual sync. Get the exec parameter

$document_nos = $db->getJdaTransaction($jdaParams);
$db->close(); //close db connection

print_r($document_nos);
if(! empty($document_nos) ) 
{
	$letdown = new letdown();
	$letdown->enterUpToLetdownMenu();

	$params = array();
	foreach($document_nos as $document_no) 
	{
		$letdown->enterEnterApproveLetdowns();
		
		$params = array('document_number' => $document_no);
		$letdown->enterApprovedLetdownQuantitiesForm();
		$validate = $letdown->enterForm($params);
		// var_dump($validate);
		if($validate)
		{
			$letdown->enterUpdateDetail();
			$validateDetail = $letdown->enterFormDetails($params);
			// var_dump($validateDetail);
			if($validateDetail) $letdown->submit($params);
		}
	}
	$letdown->logout();
}
else {
	echo " \n No rows found!. \n";
}

/*$letdown = new letdown();

$letdown->enterUpToLetdownMenu();

$document_nos = $letdown->getDocumentNo();
print_r($document_nos);
if(! empty($document_nos) ) 
{
	$params = array();
	foreach($document_nos as $document_no) {
		$letdown->enterEnterApproveLetdowns();
		
		$params = array('document_number' => $document_no);
		$letdown->enterApprovedLetdownQuantitiesForm();
		$validate = $letdown->enterForm($params);
		var_dump($validate);
		if($validate)
		{
			$letdown->enterUpdateDetail();
			$validateDetail = $letdown->enterFormDetails($params);
			var_dump($validateDetail);
			if($validateDetail) $letdown->submit($params);
		}
	}
}

$letdown->logout();*/