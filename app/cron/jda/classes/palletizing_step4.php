<?php
// chdir(dirname(__FILE__));
include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class palletizingStep4 extends jdaCustomClass 
{	
	private static $formMsg = "";
/*
Palletizing Assigning of carton to pallet/ Shipping

	13
	15
	14
	05
	1 (Palettize)
	1 (single)
	Enter Pallet id: PLTCTN20X
	press ENTER
	Enter Carton id: CTNXXX20
	press ENTER
	F1
	screenWait to (pallet_id)
	F8 (close)
	F7
	press Enter
	F1
	F1

NOTE: if multiple carton in a pallet just enter again the carton id

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

	private static function enterPalletize()
	{
		parent::$jda->screenWait("Palletize");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("1",10,2)),ENTER,true);
		echo "Entered: Palletize \n";	
	}

	private static function enterSingle()
	{
		parent::$jda->screenWait("Single");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("1",7,3)),ENTER,true);
		echo "Entered: Single \n";	
	}
	
	public function enterPalletId($pallet_id)
	{
		parent::$jda->screenWait("Pallet ID");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%9s", $pallet_id),4,3); //enter pallet id
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: Pallet ID \n";

		return self::checkResponse($pallet_id);
	}

	public function enterCartonId($data)
	{
		$carton_id = $data['box_code'];
		parent::$jda->screenWait("Carton Id");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		// $formValues[] = array(sprintf("%9s", $carton_id),7,3); //enter carton id
		$formValues[] = array($carton_id,7,3); //enter carton id
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: Carton Id \n";	

		return self::checkResponse($carton_id);
	}

	public static function pressF8()
	{
		parent::$jda->screenWait("F8");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(NULL,F8,true);
		echo "Entered: Pressed F8 \n";	
	}

	public function save($data, $pallet_code)
	{
		parent::pressF1();
		self::pressF8();
		parent::pressF7();
		#success
		if(parent::$jda->screenCheck('WRF0084')) {
			self::$formMsg = "{$pallet_code}: WRF0084: Pallet close job has been submitted to batch";
			parent::pressEnter();
			self::updateSyncStatus($pallet_code);
		}
		// parent::$jda->write5250(NULL,ENTER,true);

		// self::checkSuccess($data, $pallet_code);
	}

	private static function checkResponse($data) 
	{
		# error
		if(parent::$jda->screenCheck('WHS0173')) {
			self::$formMsg = "{$data}: WHS0173: Carton ID not on file";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			return false;
		}
		
		if(parent::$jda->screenCheck('WHS0528')) {
			self::$formMsg = "{$data}: WHS0528: Pallet ID not on file";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			return false;
		}

		if(parent::$jda->screenCheck('WHS0165')) {
			self::$formMsg = "{$data}: WHS0165: ID is already assigned to this pallet";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			return false;
		}

		if(parent::$jda->screenCheck('not equal to')) {
			self::$formMsg = "{$data}: Pallet store no not equal to carton store no";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			return false;
		}

		if(parent::$jda->screenCheck('in work status')) {
			self::$formMsg = "{$data}: Pallet must be in work status to load more cartons";
			parent::logError(self::$formMsg, __METHOD__);
			parent::pressEnter();
			return false;
		}
		#end error
		
		echo self::$formMsg;
		return true;
	}

	private static function checkSuccess($data, $pallet_code)
	{
		#success
		if(parent::$jda->screenCheck('WRF0084')) {
			self::$formMsg = "{$pallet_code}: WRF0084: Pallet close job has been submitted to batch";
			parent::pressEnter();
			self::updateSyncStatus($pallet_code);
		}
	}

	/*
	* Get all open pallets
	*/
	/*public function getPallets() 
	{
		$db = new pdoConnection();

		echo "\n Getting pallet_code from db \n";
		$sql = "SELECT DISTINCT pd.pallet_code 
				FROM wms_pallet_details pd
				INNER JOIN wms_pallet p ON p.pallet_code = pd.pallet_code AND p.sync_status = 1
				WHERE pd.sync_status = 0
				ORDER BY pd.pallet_code ASC";
		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['pallet_code'];
		}

		$db->close();

		return $result;
	}*/

	public function getCartons($pallet_code) 
	{
		$db = new pdoConnection();
		//TODOS: check if it still need to join in wms_box
		echo "\n Getting box_code from db \n";
		$sql	= "SELECT id, box_code, pallet_code
					FROM wms_pallet_details
					WHERE sync_status = 0 AND pallet_code = '{$pallet_code}'";
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
			$sql 	= "UPDATE wms_pallet_details SET sync_status = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}'
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
					WHERE sync_status = 0 AND module = 'Palletize Box' AND jda_action='Assigning' AND reference = '{$reference}'";
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
			self::enterPalletize();
			self::enterSingle();
			
		} catch (Exception $e) {
			//send fail status
			echo 'Error: '. $e->getMessage();
		}
		
	}

	private static function syncLoading($params)
	{
		$formattedString = "{$params['loadNo']}";
		$dbInstance = new pdoConnection(); //open db connection
		$dbInstance->daemon('palletizing_step5', $formattedString);
		$dbInstance->close();

		echo "Entered: Syncing loading.... \n";
	}

	public function logout($params = array())
	{	
		parent::logout();
		self::syncLoading($params);
	}
	
}

$db = new pdoConnection(); //open db connection

$jdaParams = array();
$jdaParams = array('module' => 'Palletize Box', 'jda_action' => 'Assigning');

$execParams 			= array();
$execParams['loadNo'] 	= ((isset($argv[1]))? $argv[1] : NULL);
print_r($execParams);
if(isset($argv[1])) $jdaParams['reference'] = $execParams['loadNo'];

$getPalletBox = $db->getJdaTransactionPallet($jdaParams);

print_r($getPalletBox);

if(! empty($getPalletBox) ) 
{
	$palletizing = new palletizingStep4();
	$palletizing->enterUpToSingle();
	// $getPallets = $palletizing->getPallets();
	foreach($getPalletBox as $pallet) 
	{
		$validate = $palletizing->enterPalletId($pallet);
		if($validate) 
		{
			// $cartons = array('TXT000001', 'TXT000006');
			$cartons = $palletizing->getCartons($pallet);
			$getIds = array();
			foreach($cartons as $carton)
			{
				$palletizing->enterCartonId($carton);
				$getIds[] = $carton['id'];
			}

			$palletizing->save($getIds, $pallet);
		}
	}
	$palletizing->logout($execParams);
}
else {
	echo " \n No rows found!. Proceed to loading.\n";
	$formattedString = "{$execParams['loadNo']}";
	$db->daemon('palletizing_step5', $formattedString);
}
$db->close(); //close db connection
