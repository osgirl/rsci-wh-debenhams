<?php

include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class palletizingStep3 extends jdaCustomClass
{
	private static $formMsg = "";
	private static $carrierNo = "12345"; //default
	public static $warehouseNo = "7000 ";
	public static $user = 'SYS';


/*
Palletizing Load header

	13
	04
	10
	18
	Enter control number: LOADXXX20
	press ENTER
	Enter from location: 9005
	TAB
	TAB
	TAB
	TAB: Enter shipment common carrier: 12345 (default for dev)
	TAB
	TAB
	TAB
	TAB
	TAB
	TAB
	TAB
	TAB
	TAB
	TAB
	TAB: enter created by: SYS
	F8
	TAB: Enter to Location: 20
	press ENTER
	F7
	TAB: Enter to location: 21 (if one load id has multiple store)
	F1
	F9 (approve load)
	F1
	logout

*/

	public function __construct() {
		// parent::__construct();
		parent::login();
	}

	private static function enterLoadHeaderMaintenance()
	{
		parent::$jda->screenWait("Load Header Maintenance");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("18",22,44)),ENTER,true);
		echo "Entered: Load Header Maintenance \n";
	}

	public function enterLoadControlNumber($load_code)
	{
		print_r($load_code);
		parent::$jda->screenWait("Load Control Number");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%9s", $load_code),8,42); //enter pallet id
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: Load Control Number \n";

		return self::checkResponse($load_code);
	}

	public function enterDetailForm($load_code)
	{
		parent::$jda->screenWait("Loading I.D. Number");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%5s", self::$warehouseNo), 4, 17); //enter from location
		$formValues[] = array(sprintf("%6d", self::$carrierNo), 10, 27); //enter shipment common carrier
		$formValues[] = array(self::$user, 16, 14);// enter created by
		parent::$jda->write5250($formValues,F8,true);
		echo "Entered: Detail Form \n";

		return self::checkResponse($load_code);
	}

	public function enterLocation($data)
	{
		$location = $data['store_code'];
		print_r($location);
		parent::$jda->screenWait("To Location");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%5d", $location),10,34); //enter to location
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: To Location \n";

		$isLocationValid = self::validateLocation($location);

		if($isLocationValid) self::pressF7();
	}

	/*public function enterAnotherLocation()
	{
		parent::$jda->screenWait("Load Type: M");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(NULL,F7,true);
		echo "Entered: Another Locations \n";
	}
*/
	public static function pressF1()
	{
		parent::$jda->screenWait("The record was changed");
		parent::display(parent::$jda->screen,132);

		parent::pressF1();
		echo "Entered: Warehouse Load Control Detail and self press F1 key \n";
	}

	public static function pressF7()
	{
		parent::$jda->screenWait("F7=Accept");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(NULL,F7,true);
		echo "Entered: Self Pressed F7 Key \n";
	}

	public static function pressF9($load)
	{
		parent::display(parent::$jda->screen,132);

		if(parent::$jda->screenCheck('F9=Approve') || parent::$jda->screenWait('F9=Approve'))
		{
			parent::$jda->write5250(NULL,F9,true);
			self::updateSyncStatus($load);
			echo "Entered: Self Pressed F9 Key \n";
		}
		else {
			parent::pressF1();
			echo "Entered: Missing F9 Key \n";
		}
	}

	/*public static function pressF9($ids)
	{
		parent::$jda->screenWait("F9=Approve");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(NULL,F9,true);
		self::updateSyncStatus($ids);
		echo "Entered: Self Pressed F9 Key \n";
	}*/

	public function save($loadCode)
	{
		self::pressF1();
		self::pressF9($loadCode);
		echo "Entered: Saving.. \n";
	}

	private static function checkResponse($data)
	{
		# error
		if(parent::$jda->screenCheck('The load control i.d. may not be blank')) {
			self::$formMsg = "{$data}: The load control i.d. may not be blank.";
			parent::logError(self::$formMsg, __METHOD__);
			return false;
		}

		if(parent::$jda->screenCheck('The carrier entered is not valid')) {
			self::$formMsg = "{self::$carrierNo}: The carrier entered is not valid";
			parent::logError(self::$formMsg, __METHOD__);
			// parent::pressF1();
			return false;
		}

		if(parent::$jda->screenCheck('The from location is not a valid location')) {
			self::$formMsg = "{self::$warehouseNo}: The from location is not a valid location";
			parent::logError(self::$formMsg, __METHOD__);
			// parent::pressF1();
			return false;
		}

		if(parent::$jda->screenCheck('The clerk entered is not valid for the from location')) {
			self::$formMsg = "{self::$user}: The clerk entered is not valid for the from location";
			parent::logError(self::$formMsg, __METHOD__);
			// parent::pressF1();
			return false;
		}
		echo self::$formMsg;
		return true;
	}

	private static function validateLocation($data)
	{
		if(parent::$jda->screenCheck('The to location may not be blank')) {
			self::$formMsg = "{$data}: The to location may not be blank";
			parent::logError(self::$formMsg, __METHOD__);
			// parent::pressF1();
			// parent::pressF1();
			return false;
		}
		echo self::$formMsg;
		return true;
	}

	/*
	* Get all open pallets
	*/
	/*public function getLoads()
	{
		$db = new pdoConnection();

		echo "\n Getting load_code from db \n";
		$sql 	= "SELECT DISTINCT load_code
					FROM wms_load_details ld
					LEFT JOIN wms_pallet p ON p.pallet_code = ld.pallet_code AND p.pallet_code = 1
					WHERE ld.sync_status = 0
					ORDER BY ld.load_code ASC";
		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['load_code'];
		}

		$db->close();

		return $result;
	}*/

	public function getLocations($load_code)
	{
		$db = new pdoConnection();

		echo "\n Getting load_code from db \n";
		$sql	= "SELECT ld.id, ld.pallet_code, ld.load_code,pallet.store_code
					FROM wms_load_details ld
					INNER JOIN wms_pallet pallet ON pallet.pallet_code = ld.pallet_code
					WHERE ld.sync_status = 0 AND ld.load_code = '{$load_code}'";
		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value;
		}

		$db->close();

		return $result;
	}

	/*
	* Update batch wms_load_details sync_status
	*/
	/*private static function updateSyncStatus($ids, $isError = FALSE)
	{
		$db = new pdoConnection();
		$date_today = date('Y-m-d H:i:s');

		$status = ($isError) ? parent::$errorFlag : parent::$successFlag;

		echo "\n Updating... \n";
		if(! empty($ids) )
		{
			$ids = join(',', $ids);
			$sql 	= "UPDATE wms_load_details SET sync_status = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}'
						WHERE sync_status = 0 AND id IN ({$ids})";
			$query 	= $db->exec($sql);
			echo "Affected rows: $query \n";
		}
		else {
			print_r($ids);
			echo "Empty ids \n";
		}

		$db->close();
	}*/

	/*
	* Update ewms trasaction_to_jda sync_status
	*/
	private static function updateSyncStatus($reference, $isError = FALSE)
	{
		$db = new pdoConnection();
		$date_today = date('Y-m-d H:i:s');

		$status = ($isError) ? parent::$errorFlag : parent::$successFlag;

		echo "\n Getting receiver no from db \n";
		$sql 	= "UPDATE wms_transactions_to_jda
					SET sync_status = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}'
					WHERE sync_status = 0 AND module = 'Load Header' AND jda_action='Creation' AND reference = '{$reference}'";
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";
		$db->close();
	}

	/*
	* On done only via android
	*/
	public function enterUpToLoadHeaderMaintenance()
	{
		//TODO::checkvalues
		//TODO::how to know if error
		try {
			$title = "Palletizing Load header \n";
			echo $title ;
			parent::checkRecoverJob();
			parent::checkJobOnProgress();
			parent::enterDistributionManagement();
			parent::enterWarehouseMaintenance();
			parent::enterCartonPalletLoadMaintenance();
			self::enterLoadHeaderMaintenance();

		} catch (Exception $e) {
			//send fail status
			echo 'Error: '. $e->getMessage();
		}

	}

	private static function syncBoxesToPallet($params)
	{
		$formattedString = "{$params['loadNo']}";
		$dbInstance = new pdoConnection(); //open db connection
		$dbInstance->daemon('palletizing_step4', $formattedString);
		$dbInstance->close();

		echo "Entered: Syncing boxes to pallet.... \n";
	}

	public function logout($params = array())
	{
		parent::logout();
		self::syncBoxesToPallet($params);
	}

}


$db = new pdoConnection(); //open db connection

$jdaParams = array();
$jdaParams = array('module' => 'Pallet Header', 'jda_action' => 'Creation', 'checkSuccess' => 'true');

$execParams 			= array();
$execParams['loadNo'] 	= ((isset($argv[1]))? $argv[1] : NULL);

print_r($execParams);
if(isset($argv[1])) $jdaParams['reference'] = $execParams['loadNo'];

$getUnsuccessfulPallets = $db->getJdaTransactionPallet($jdaParams);

if(empty($getUnsuccessfulPallets)){
	$jdaParams = array();
	$jdaParams = array('module' => 'Load Header', 'jda_action' => 'Creation');

	if(isset($argv[1])) $jdaParams['reference'] = $execParams['loadNo'];

	$getLoads = $db->getJdaTransaction($jdaParams);

	print_r($getLoads);

	if(! empty($getLoads) )
	{
		$palletizing = new palletizingStep3();
		$palletizing->enterUpToLoadHeaderMaintenance();
		// $getLoads = $palletizing->getLoads();
		foreach($getLoads as $load)
		{
			$validate = $palletizing->enterLoadControlNumber($load);
			if($validate)
			{
				$validateDetail = $palletizing->enterDetailForm($load);
				if($validateDetail)
				{
					//get all location
					$getLocations = $palletizing->getLocations($load);
					$getIds = array();
					foreach($getLocations as $location)
					{
						$isLocationValid = $palletizing->enterLocation($location);
						// if($isLocationValid) $palletizing->enterAnotherLocation();
						$getIds[] = $location['id'];
					}
					// $palletizing->save($getIds);
					$palletizing->save($load);
				}
			}
		}
		$palletizing->logout($execParams);
	}
	else {
		echo " \n No rows found!. Proceed to assigning boxes to pallet.  \n";
		$formattedString = "{$execParams['loadNo']}";
		$db->daemon('palletizing_step4', $formattedString);
	}
}
else{
	echo " \n Found unsuccessful creation of pallet headers! Stop process!\n";
}
$db->close(); //close db connection