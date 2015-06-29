<?php

include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class manualMove extends jdaCustomClass
{
	private static $formMsg = "";

	public static $user = 'SYS';
	public static $warehouseNo = "7000 ";

	/*
	NOTE!!!! cron needed. possibility of connection timeout
		13
		04
		03
		03
		Enter location: 7000 (warehouse)
		enter SKU
		clerk initials: SYS
		from slot
		to slot
		Quantity
		F7
	*/

	public function __construct() {
		// parent::__construct();
		self::$formMsg = __METHOD__;
		parent::logError(self::$formMsg, __METHOD__);
		parent::login();
	}

	private static function enterDistribution()
	{
		#enter Distribution Center Management
		parent::$jda->screenWait("Distribution Center Management");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("13",22,44)),ENTER,true);
		echo "Entered: Distribution Center Management \n";
	}

	private static function enterMoveGeneralMaintenance()
	{
		parent::$jda->screenWait("Move and General Maintenance");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("03",22,44)),ENTER,true);
		echo "Entered: Move and General Maintenance \n";
	}

	private static function enterManualMoves()
	{
		parent::$jda->screenWait("Manual Moves");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("03",22,44)),ENTER,true);
		echo "Entered: Manual Moves \n";
	}

	public function enterMMForm($move_detail)
	{
		$formValues = array();

		parent::$jda->screenWait("warehouse Inventory Movement");
		parent::display(parent::$jda->screen,132);
		$formValues[] = array(sprintf("%5s", self::$warehouseNo),4,32);
		$formValues[] = array($move_detail['sku'],6,32);
		$formValues[] = array(self::$user,11,32);
		$formValues[] = array($move_detail['from_slot'],13,32);
		$formValues[] = array($move_detail['to_slot'],14,32);
		$formValues[] = array($move_detail['quantity'],15,32);
		parent::$jda->write5250($formValues,F7,true);
		parent::display(parent::$jda->screen,132);
		$validate =  self::checkingInput($move_detail['id'],__METHOD__);
		if($validate)
			self::checkMoveLanding($move_detail['id']);
	}

	public function checkingInput($input,$source)
	{
		if(parent::$jda->screenCheck('Warehouse number is not valid or is not a warehouse')) {
            $message="Warehouse number is not valid or is not a warehouse";
            self::$formMsg = "{$input}: {$message}";
            parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($input,"{$source}: {$message}", TRUE);
			return false;
		}
		if(parent::$jda->screenCheck("Invalid 'sku' entered")) {
            $message="Invalid sku entered";
            self::$formMsg = "{$input}: {$message}";
            parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($input,"{$source}: {$message}", TRUE);
			return false;
		}
		if(parent::$jda->screenCheck("Clerk is not valid")) {
            $message="Clerk is not valid";
            self::$formMsg = "{$input}: {$message}";
            parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($input,"{$source}: {$message}", TRUE);
			return false;
		}
		if(parent::$jda->screenCheck("'From slot' or 'new primary slot' must be entered")) {
            $message="From slot or new primary slot must be entered";
            self::$formMsg = "{$input}: {$message}";
            parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($input,"{$source}: {$message}", TRUE);
			return false;
		}
		if(parent::$jda->screenCheck("Slot not valid for this warehouse")) {
            $message="Slot not valid for this warehouse";
            self::$formMsg = "{$input}: {$message}";
            parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($input,"{$source}: {$message}", TRUE);
			return false;
		}
		if(parent::$jda->screenCheck("Slot not valid for this sku")) {
            $message="Slot not valid for this sku";
            self::$formMsg = "{$input}: {$message}";
            parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($input,"{$source}: {$message}", TRUE);
			return false;
		}
		if(parent::$jda->screenCheck("Quantity requested to move exceeds quantity available")) {
            $message="Quantity requested to move exceeds quantity available";
            self::$formMsg = "{$input}: {$message}";
            parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($input,"{$source}: {$message}", TRUE);
			return false;
		}
		echo self::$formMsg;

		return true;
	}

	//check if move is successful
	private static function checkMoveLanding($id) {
		if(parent::$jda->screenWait('moved from')) {
			echo "\n SKU has been successfully moved \n";
			self::updateSyncStatus($id);
		}
	}

	/*
	* Update ewms trasaction_to_jda sync_status
	*/
	private static function updateSyncStatus($reference,$error_message=null, $isError = FALSE) {
		$db = new pdoConnection();
		$date_today = date('Y-m-d H:i:s');

		$status = ($isError) ? parent::$errorFlag : parent::$successFlag;

		echo "\n Getting transfer no from db \n";
		$sql 	= "UPDATE wms_manual_move
					SET sync_status = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}', error_message = '{$error_message}'
					WHERE sync_status = 0 AND id = {$reference} ";
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";
		$db->close();
	}

	/*
	* On done only via android
	*/
	public function enterUpToManualMoves()
	{
		//TODO::checkvalues
		//TODO::how to know if error
		try {
			$title = "Manual Move \n";
			echo $title ;
			parent::checkRecoverJob();
			parent::checkJobOnProgress();
			self::enterDistribution();
			parent::enterWarehouseMaintenance();
			self::enterMoveGeneralMaintenance();
			self::enterManualMoves();
		} catch (Exception $e) {
			//send fail status
			echo 'Error: '. $e->getMessage();
		}
	}

	public function logout()
	{
		parent::logout();

		echo "Entered: Done manual move.... \n";
	}
}

$db = new pdoConnection(); //open db connection

$mmId = null;

$execParams 			= array();
$execParams['mmId'] 	= ((isset($argv[1]))? $argv[1] : NULL);
if($argv[1]) $mmId = $execParams['mmId'];

$move_details = $db->getMoveDetails($mmId);

if(! empty($move_details) )
{
	print_r($move_details);
	$manualMove = new manualMove();
	$manualMove->enterUpToManualMoves();
	foreach ($move_details as $move_detail) {
		$manualMove->enterMMForm($move_detail);
	}
	$manualMove->logout($execParams);

}
else {
	echo " \n No rows found!. \n";
}
$db->close(); //close db connection