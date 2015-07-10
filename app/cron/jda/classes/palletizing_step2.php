<?php

include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class palletizingStep2 extends jdaCustomClass
{
	private static $formMsg = "";
	private static $palletType = "S"; //default
	public static $warehouseNo = "7000 ";
	public static $user = 'SYS';


/*
Palletizing Maintaining of Cartoon Pallet

	13
	04
	10
	06
	Enter Pallet type: S (default)
	press ENter
	Enter Pallet Id: PLCTNXXYY
	press Enter
	Enter create clerk: SYS
	Enter from loc: 9005 (default)
	Enter to loc: 20
	F7

*/

	public function __construct() {
		// parent::__construct();
		parent::login();
	}

	private static function enterPalletHeaderMaintenance()
	{
		parent::$jda->screenWait("Pallet Header Maintenance");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("06",22,44)),ENTER,true);
		echo "Entered: Pallet Header Maintenance \n";
	}

	private static function enterPalletType($pallet_code)
	{
		parent::$jda->screenWait("Enter Pallet Type");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(self::$palletType, 8, 40);// enter pallet type
		#make sure to empty the pallet id
		if(parent::$jda->screenCheck('Enter Pallet I.D')) {
			$empty_pallet = "         ";
			$formValues[] = array(sprintf("%9s", $empty_pallet),11,40); //enter pallet id
		}
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: Pallet Type \n";

		return self::checkResponse($pallet_code,__METHOD__);
	}

	private static function enterPalletId($pallet_code)
	{
		parent::$jda->screenWait("Enter Pallet I.D");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%9s", $pallet_code),11,40); //enter pallet id
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: Pallet I.D \n";

		return self::checkResponse($pallet_code,__METHOD__);
	}

	private static function enterDetailForm($pallet)
	{
		print_r($pallet);
		$pallet_code = $pallet['pallet_code'];
		$toLocation = $pallet['store_code'];

		parent::$jda->screenWait("Create Clerk");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(self::$user, 8, 57);// enter create clerk
		$formValues[] = array(sprintf("%5s", self::$warehouseNo), 9, 17); //enter from location
		$formValues[] = array($toLocation, 10, 17); //enter from location
		parent::$jda->write5250($formValues,F7,true);
		echo "Entered: Detailed Form \n";

		return self::checkResponse($pallet_code,__METHOD__);
	}

	public function save($pallet)
	{
		$pallet_code = $pallet['pallet_code'];
		self::enterPalletType($pallet_code);
		self::enterPalletId($pallet_code);
		self::enterDetailForm($pallet);
	}

	private static function checkResponse($pallet_code,$source)
	{
		# error
		if(parent::$jda->screenCheck('Load id does not have the "to" location established')) {
            $receiver_message="Load id does not have the to location established";
			self::$formMsg = "{self::$palletType}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($pallet_code,"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Pallet type selection code is not valid or blank')) {
            $receiver_message="Pallet type selection code is not valid or blank";
			self::$formMsg = "{self::$palletType}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($pallet_code,"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('The pallet id number must be entered')) {
            $receiver_message="The pallet id number must be entered";
			self::$formMsg = "{$pallet_code}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			return false;
		}

		if(parent::$jda->screenCheck('The clerk entered is not valid for the from location')) {
            $receiver_message="The clerk entered is not valid for the from location";
			self::$formMsg = "{self::$user}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($pallet_code,"{$source}: {$receiver_message}", TRUE);
			parent::pressF1();
			parent::enterWarning();
			return false;
		}

		#success
		if(parent::$jda->screenCheck('Record added to system') || parent::$jda->screenWait('Record added to system')) {
			self::$formMsg = "{$pallet_code}: Record added to system";
			self::updateSyncStatus($pallet_code);
		}

		#success
		if(parent::$jda->screenCheck('The selected record has been updated') || parent::$jda->screenWait('The selected record has been updated')) {
			self::$formMsg = "{$pallet_code}: The selected record has been updated";
			self::updateSyncStatus($pallet_code);
		}

		echo self::$formMsg;

		return true;
	}

	/*
	* Get all open pallets
	*/
	/*public function getPallets()
	{
		$db = new pdoConnection();

		echo "\n Getting box codes from db \n";
		$sql = "SELECT p.pallet_code, p.store_code
				FROM wms_pallet p
				RIGHT JOIN wms_pallet_details pd ON p.pallet_code = pd.pallet_code
				RIGHT JOIN wms_box_details bd ON pd.box_code = bd.box_code AND bd.sync_status = 1
				INNER JOIN wms_box b ON bd.box_code = b.box_code AND b.sync_status = 1
				WHERE p.sync_status = 0
				GROUP BY p.pallet_code, p.store_code
				ORDER BY p.pallet_code ASC";
		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value;
		}

		$db->close();

		return $result;
	}*/

	/*
	* Update ewms trasaction_to_jda sync_status
	*/
	/*private static function updateSyncStatus($pallet_code, $isError = FALSE)
	{
		$db = new pdoConnection();
		$date_today = date('Y-m-d H:i:s');

		$status = ($isError) ? parent::$errorFlag : parent::$successFlag;

		echo "\n Updating... \n";
		$sql 	= "UPDATE wms_pallet SET sync_status = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}'
					WHERE sync_status = 0 AND pallet_code = '{$pallet_code}'";
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
					WHERE sync_status = 0 AND module = 'Pallet Header' AND jda_action='Creation' AND reference = '{$reference}'";
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";
		$db->close();
	}

	/*
	* On done only via android
	*/
	public function enterUpToPalletHeaderMaintenance()
	{
		//TODO::checkvalues
		//TODO::how to know if error
		try {
			$title = "Palletizing Maintaining of Cartoon Pallet \n";
			echo $title ;
			parent::checkRecoverJob();
			parent::checkJobOnProgress();
			parent::enterDistributionManagement();
			parent::enterWarehouseMaintenance();
			parent::enterCartonPalletLoadMaintenance();
			self::enterPalletHeaderMaintenance();

		} catch (Exception $e) {
			//send fail status
			echo 'Error: '. $e->getMessage();
		}

	}

	private static function syncLoadHeader($params)
	{
		$formattedString = "{$params['loadNo']}";
		$dbInstance = new pdoConnection(); //open db connection
		$dbInstance->daemon('palletizing_step3', $formattedString);
		$dbInstance->close();

		echo "Entered: Syncing load header.... \n";
	}

	public function logout($params = array())
	{
		parent::logout();
		self::syncLoadHeader($params);
	}

}

$db = new pdoConnection(); //open db connection

$jdaParams = array();
$jdaParams = array('module' => 'Box Header', 'jda_action' => 'Creation', 'checkSuccess' => 'true');

// format: php picklist.php {docNo} {$boxNo} {$palletNo} {$loadNo}
$execParams 			= array();
$execParams['loadNo'] 	= ((isset($argv[1]))? $argv[1] : NULL);

print_r($execParams);
if(isset($argv[1])) $jdaParams['reference'] = $execParams['loadNo'];

$getUnsuccessfulBoxes = $db->getJdaTransactionBoxHeader($jdaParams);

if(empty($getUnsuccessfulBoxes)){
	$jdaParams = array();
	$jdaParams = array('module' => 'Pallet Header', 'jda_action' => 'Creation');

	if(isset($argv[1])) $jdaParams['reference'] = $execParams['loadNo'];

	$getPallets = $db->getJdaTransactionPallet($jdaParams);
	print_r($getPallets);
	if(! empty($getPallets) )
	{
		$palletsInfo = $db->getPalletsInfo($getPallets);
		$palletizing = new palletizingStep2();
		$palletizing->enterUpToPalletHeaderMaintenance();

		foreach($palletsInfo as $pallet) {
			$palletizing->save($pallet);
		}
		$palletizing->logout($execParams);
	}
	else {
		echo " \n No rows found!. Proceed to load header creation\n";
		$formattedString = "{$execParams['loadNo']}";
		$db->daemon('palletizing_step3', $formattedString);
	}
}
else{
	echo " \n Found unsuccessful creation of box headers! Stop process!\n";
}

$db->close(); //close db connection
