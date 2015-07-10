<?php

include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class palletizingStep5 extends jdaCustomClass
{
	private static $formMsg = "";
	private static $sealNo = 1;
	private static $weight = 1;
/*
Palletizing: Loading

	13
	15
	14
	05
	2
	1
	Enter Load id: LOADXXX20 Note: check for error: Enter a pallet id
	Enter pallet_id: PLTCTN20X
	Enter weight: 1
	press ENTER
	Enter again pallet_id //if multiple pallet just loop here
	F1
	F8
	Enter seal no: 1 (default)
	F7
	press Enter
	F1
	F1
	sign off


*/

	public function __construct() {
		// parent::__construct();
		parent::login();
	}

	private static function enterRadioFrequencyApplications()
	{
		parent::$jda->screenWait("Radio Frequency Applications");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("15",22,44)),ENTER,true);
		echo "Entered: Radio Frequency Applications \n";
	}

	private static function enterRFApplications()
	{
		parent::$jda->screenWait("RF Applications");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("14",22,44)),ENTER,true);
		echo "Entered: RF Applications \n";
	}

	private static function enterShipping()
	{
		parent::$jda->screenWait("Shipping");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("05",15,1)),ENTER,true);
		echo "Entered: Shipping \n";
	}

	private static function enterLoading()
	{
		parent::$jda->screenWait("Loading");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("2",10,2)),ENTER,true);
		echo "Entered: Loading \n";
	}

	private static function enterSingle()
	{
		parent::$jda->screenWait("Single");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("1",7,3)),ENTER,true);
		echo "Entered: Single \n";
	}

	public function enterBuildSingle($load_code)
	{
		print_r($load_code);
		parent::$jda->screenWait("Build Single");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%9s", $load_code),5,4); //enter load id
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: Build Single \n";

		return self::checkResponse($load_code);
	}

	public function enterPalletId($data)
	{
		$pallet_id = $data['pallet_code'];
		print_r($pallet_id);
		parent::$jda->screenWait("Pallet Id");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%9s", $pallet_id),6,3); //enter pallet id
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: Pallet Id \n";

		return self::checkResponse($pallet_id);
	}

	public function enterWeight($pallet_id)
	{
		parent::$jda->screenWait("Weight");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%12d", self::$weight),8,3); //enter pallet id
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: Weight \n";

		return self::checkResponse($pallet_id);
	}

	public static function pressF1()
	{
		parent::$jda->screenWait("F1");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(NULL,F1,true);
		echo "Entered: Pressed F1 \n";
	}

	public static function pressF8($load)
	{
		parent::$jda->screenWait("F8");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(NULL,F8,true);
		echo "Entered: Pressed F8 \n";

		// self::checkResponse(NULL, $load);

	}

	private static function enterSealNo($load)
	{
		parent::$jda->screenWait("Seal No");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%9d", self::$sealNo),7,4); //enter seal no
		parent::$jda->write5250($formValues,F7,true);
		echo "Entered: Seal No \n";

		return self::checkSuccess($load);
	}

	public function save($ids, $load)
	{
		self::pressF1();
		self::pressF8($load);
		self::checkResponse(NULL, $load);
		self::enterSealNo($load);
	}

	private static function checkResponse($data = NULL, $load = NULL)
	{
		# error
		if(parent::$jda->screenCheck('WRF0034')) {
			self::$formMsg = "{$data}: WRF0034: Enter a load id";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			return false;
		}

		if(parent::$jda->screenCheck('WRF0132')) {
			self::$formMsg = "{$data}: WRF0132: The load id entered does not exist";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			return false;
		}

		if(parent::$jda->screenCheck('WRF0035')) {
			self::$formMsg = "{$data}: WRF0035: Enter a pallet id";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			return false;
		}

		if(parent::$jda->screenCheck('WRF0058')) {
			self::$formMsg = "{$data}: WRF0058: Neither a pallet id or a carton id exists with the label entered";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			return false;
		}

		#TODOS: sync_status for this should be 2
		if(parent::$jda->screenCheck('WRF0169')) {
			self::$formMsg = "{$data}: WRF0169: The selected id has already been loaded";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			// self::updateLoadStatusByPallet($data, TRUE); //$data = pallet_code
			return false;
		}

		if(parent::$jda->screenCheck('WRF0091')) {
			self::$formMsg = "{$data}: WRF0091: Pallet must be closed before loading";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			return false;
		}

		if(parent::$jda->screenCheck('WRF0170')) {
			self::$formMsg = "{$data}: WRF0170: The selected id is not in load status";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			// self::updateLoadStatusByPallet($data, TRUE); //$data = pallet_code
			return false;
		}

		if(parent::$jda->screenCheck('WRF0036')) {
			self::$formMsg = "{$data}: WRF0036: Enter pallet weight";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			return false;
		}

		if(parent::$jda->screenCheck('WRF0191')) {
			self::$formMsg = "{$load}: There are more pallets that can be loaded.";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			parent::pressEnter();
			echo 'Entered: Pressed ENTER key again.';
			return false;
			// return false;
		}

		echo self::$formMsg;
		return true;
	}

	private static function checkSuccess($loadCode)
	{
		#success
		if(parent::$jda->screenCheck('WRF0028') || parent::$jda->screenWait('WRF0028')) {
			self::$formMsg = "WRF0028: Close load job has been submitted to batch";
			parent::pressEnter();
			// self::updateLoadStatusByIds($data);
			self::updateSyncStatus($loadCode);
		}

		echo self::$formMsg;
		return true;
	}

	/*
	* Get all open is_load pallets
	*/
	/*public function getLoads()
	{
		$db = new pdoConnection();

		echo "\n Getting load_code from db \n";
		$sql 	= "SELECT DISTINCT load_code FROM wms_load_details WHERE is_load = 0 AND sync_status = 1
					ORDER BY load_code ASC";

		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['load_code'];
		}

		$db->close();

		return $result;
	}*/

	public function getPallets($load_code)
	{
		$db = new pdoConnection();

		echo "\n Getting load_code from db \n";
		$sql	= "SELECT id, pallet_code, load_code
					FROM wms_load_details
					WHERE is_load = 0 AND load_code = '{$load_code}'";
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
	/*private static function updateLoadStatusByIds($ids, $isError = FALSE)
	{
		$db = new pdoConnection();
		$date_today = date('Y-m-d H:i:s');

		$status = ($isError) ? parent::$errorFlag : parent::$successFlag;

		echo "\n Updating... \n";
		if(! empty($ids) )
		{
			$ids = join(',', $ids);
			$sql 	= "UPDATE wms_load_details SET is_load = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}'
						WHERE id IN ({$ids})";
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
	* Update batch wms_load_details sync_status
	*/
	/*private static function updateLoadStatusByPallet($pallet_code, $isError = FALSE)
	{
		$db = new pdoConnection();
		$date_today = date('Y-m-d H:i:s');

		$status = ($isError) ? parent::$errorFlag : parent::$successFlag;

		echo "\n Updating... \n";
		$sql 	= "UPDATE wms_load_details SET is_load = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}'
					WHERE is_load = 0 AND pallet_code = '{$pallet_code}'";
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";

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
					WHERE sync_status = 0 AND module = 'Loading' AND jda_action='Assigning' AND reference = '{$reference}'";
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";
		$db->close();
	}

	/*
	* On done only via android
	*/
	public function enterUpToSingle()
	{
		//TODO::checkvalues
		//TODO::how to know if error
		try {
			$title = "Shipping \n";
			echo $title ;
			parent::checkRecoverJob();
			parent::checkJobOnProgress();
			parent::enterDistributionManagement();
			self::enterRadioFrequencyApplications();
			self::enterRFApplications();
			self::enterShipping();
			self::enterLoading();
			self::enterSingle();

		} catch (Exception $e) {
			//send fail status
			echo 'Error: '. $e->getMessage();
		}

	}

	private static function syncShipping($params)
	{
		$formattedString = "{$params['loadNo']}";
		$dbInstance = new pdoConnection(); //open db connection
		$dbInstance->daemon('palletizing_step6', $formattedString);
		$dbInstance->close();

		echo "Entered: Syncing shipping.... \n";
	}

	public function logout($params = array())
	{
		parent::logout();
		self::syncShipping($params);
	}

}

$db = new pdoConnection(); //open db connection

$jdaParams = array();
$jdaParams = array('module' => 'Palletize Box', 'jda_action' => 'Assigning', 'checkSuccess' => 'true');

$execParams 			= array();
$execParams['loadNo'] 	= ((isset($argv[1]))? $argv[1] : NULL);
print_r($execParams);
if(isset($argv[1])) $jdaParams['reference'] = $execParams['loadNo'];

$getUnsuccessfulPalletBox = $db->getJdaTransactionPallet($jdaParams);

if(empty($getUnsuccessfulPalletBox))
{
	$jdaParams = array();
	$jdaParams = array('module' => 'Loading', 'jda_action' => 'Assigning');

	if(isset($argv[1])) $jdaParams['reference'] = $execParams['loadNo'];

	$getLoads = $db->getJdaTransaction($jdaParams);

	print_r($getLoads);

	if(! empty($getLoads) )
	{
		$loading = new palletizingStep5();
		$loading->enterUpToSingle();

		// $getLoads = $loading->getLoads();
		foreach($getLoads as $load)
		{
			$validate = $loading->enterBuildSingle($load);
			if($validate)
			{
				$getPallets = $loading->getPallets($load);
				$ids = array();
				foreach($getPallets as $pallet)
				{
					$isValidPallet = $loading->enterPalletId($pallet);
					if($isValidPallet) $loading->enterWeight($pallet);
					$ids[] = $pallet['id'];
				}
				$loading->save($ids, $load);
			}
		}
		$loading->logout($execParams);
	}
	else {
		echo " \n No rows found!. Proceed to shipping.\n";
		$formattedString = "{$execParams['loadNo']}";
		$db->daemon('palletizing_step6', $formattedString);
	}
}
else{
	echo " \n Found unsuccessful assigning of palletize box! Stop process!\n";
}
$db->close(); //close db connection
