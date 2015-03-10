<?php

include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class palletizingStep6 extends jdaCustomClass 
{	
	private static $formMsg = "";
	private static $weight = 1;
/*
Palletizing: Shipping

	13
	15
	14
	05
	3
	Enter load id: LOADXXX20
	press Enter
	Enter actual weight: 1 (default)
	F7
	press ENTER


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

	private static function enterShippingAgain()
	{
		parent::$jda->screenWait("Transfer Shipping");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("3",10,2)),ENTER,true);
		echo "Entered: Shipping Again \n";	
	}

	public function enterLoadId($load_code)
	{
		parent::$jda->screenWait("Load ID");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%9s", $load_code),4,3); //enter pallet id
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: Load id \n";

		return self::checkResponse($load_code,__METHOD__);
	}

	public static function pressEnter()
	{
		parent::$jda->screenWait("Loading Door");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(NULL,ENTER,true);
		echo "Entered: Pressed Enter Key \n";
	}

	private static function enterActualWeight($load_code)
	{
		parent::$jda->screenWait("Actual Weight");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%12d", self::$weight),5,3); //enter pallet id
		parent::$jda->write5250($formValues,F7,true);
		echo "Entered: Actual Weight \n";

		return self::checkResponse($load_code,__METHOD__);
	}

	/*public static function pressF7($load_code)
	{
		parent::$jda->screenWait("F7");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(NULL,F7,true);
		echo "Entered: Pressed F7 Key \n";	

		return self::checkResponse($load_code);
	}*/

	public function save($load_code)
	{
		self::pressEnter();
		self::enterActualWeight($load_code);
		// self::pressF7($load_code);
	}

	private static function checkResponse($load_code,$source)
	{
		# error
		if(parent::$jda->screenCheck('WRF0133')) {
			self::$formMsg = "{$load_code}: WRF0133: The load id must be entered";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			return false;
		}

		if(parent::$jda->screenCheck('WRF0134')) {
            $receiver_message="WRF0134: The load id does not exist";
			self::$formMsg = "{$load_code}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($load_code,"{$source}: {$receiver_message}", TRUE);
			parent::pressEnter();
			return false;
		}

		if(parent::$jda->screenCheck('WRF0135')) {
            $receiver_message="WRF0135: The load id must not be in a closed status";
			self::$formMsg = "{$load_code}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($load_code,"{$source}: {$receiver_message}", TRUE);
			parent::pressEnter();
			return false;
		}
		

		#success
		if(parent::$jda->screenCheck('WRF0052')) {
			self::$formMsg = "{$load_code}: WRF0052: The load approval job has been submitted to batch";
			self::updateSyncStatus($load_code);
			parent::pressEnter();
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
		$sql = "SELECT l.load_code 
				FROM wms_load l
				INNER JOIN wms_load_details ld ON l.load_code = ld.load_code AND is_load = 1
				WHERE l.sync_status = 0 AND is_shipped = 1
				ORDER BY l.load_code ASC";
		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['load_code'];
		}

		$db->close();

		return $result;
	}*/

	/*
	* Update batch wms_load_details sync_status
	*/
	/*private static function updateSyncStatus($load_code, $isError = FALSE) 
	{
		$db = new pdoConnection();
		$date_today = date('Y-m-d H:i:s');

		$status = ($isError) ? parent::$errorFlag : parent::$successFlag;

		echo "\n Updating... \n";
		$sql 	= "UPDATE wms_load SET sync_status = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}'
					WHERE sync_status = 0 AND load_code = '{$load_code}'";
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
					WHERE sync_status = 0 AND module = 'Shipping' AND jda_action='Shipping' AND reference = '{$reference}'";
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";
		$db->close();
	}

	/*
	* On done only via android
	*/
	public function enterUpToShippingAgain()
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
			self::enterShippingAgain();
			
		} catch (Exception $e) {
			//send fail status
			echo 'Error: '. $e->getMessage();
		}
		
	}

	public function logout()
	{	
		parent::logout();

		echo "Entered: Done shipping.... \n";
	}
	
}

$db = new pdoConnection(); //open db connection

$jdaParams = array();
$jdaParams = array('module' => 'Shipping', 'jda_action' => 'Shipping');
if($argv[1]) $jdaParams['reference'] = $argv[1];// for manual sync. Get the exec parameter

$getLoads = $db->getJdaTransaction($jdaParams);
$db->close(); //close db connection
print_r($getLoads);

if(! empty($getLoads) ) 
{
	$shipping = new palletizingStep6();
	$shipping->enterUpToShippingAgain();

	// $getLoads = $shipping->getLoads();
	foreach($getLoads as $load) 
	{
		$validate = $shipping->enterLoadId($load);
		if($validate) $shipping->save($load);
	}
	$shipping->logout();
}
else {
	echo " \n No rows found!. \n";
}
