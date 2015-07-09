<?php

include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class palletizingStep1 extends jdaCustomClass
{
	private static $formMsg = "";
	private static $cartonType = 'S'; //default
	public static $user = 'SYS';


/*
Palletizing Maintaining of Cartoon header

	13
	04
	10
	03
	ENTER carton type: S (default na to)
	press ENTER
	Enter Carton id: CTNXXX20
	press Enter
	F7
*/

	public function __construct() {
		// parent::__construct();
		parent::login();
	}

	private static function enterCartonHeaderMaintenance()
	{
		parent::$jda->screenWait("Carton Header Maintenance");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("03",22,44)),ENTER,true);
		echo "Entered: Carton Header Maintenance \n";
	}

	private static function enterCartonType($box_code)
	{
		parent::$jda->screenWait("Enter Carton Type");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(self::$cartonType, 8, 40);// enter carton type
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: Carton Type \n";

		return self::checkResponse($box_code,__METHOD__);
	}

	private static function enterCartonId($box_code)
	{
		parent::$jda->screenWait("Enter Carton ID");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		// $formValues[] = array(sprintf("%9s", $box_code),10,40); //enter carton id
		$formValues[] = array($box_code,10,40); //enter carton id
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: Carton ID \n";

		return self::checkResponse($box_code,__METHOD__);
	}

	private static function enterCartonDetails($box_code)
	{
		parent::$jda->screenWait("Cube Total");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(NULL,F7,true);
		echo "Entered: Carton Details \n";

		return self::checkResponse($box_code,__METHOD__);
	}

	public function save($box_code)
	{
		self::enterCartonType($box_code);
		self::enterCartonId($box_code);
		self::enterCartonDetails($box_code);
	}

	private static function checkResponse($box_code,$source)
	{
		# error
		if(parent::$jda->screenCheck('Carton type selection code is not valid or blank')) {
            $receiver_message="Carton type selection code is not valid or blank";
			self::$formMsg = "{self::$cartonType}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($box_code,"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		// no need to log this, this will always happen as we press ENTER key
		if(parent::$jda->screenCheck('This carton id is invalid')) {
			self::$formMsg = "{$box_code}: This carton id is invalid";
		}

		//TODOS: what to do if this message occured
		if(parent::$jda->screenCheck('This is a new record. Press F1 to bypass record add')) {
            $receiver_message="This is a new record. Press F1 to bypass record add";
			self::$formMsg = "{$box_code}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($box_code,"{$source}: {$receiver_message}", TRUE);
			parent::pressF1();
			parent::enterWarning();
			return false;
		}

		if(parent::$jda->screenCheck('The from location entered is not valid')) {
            $receiver_message="The from location entered is not valid";
			self::$formMsg = "{$box_code}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($box_code,"{$source}: {$receiver_message}", TRUE);
			parent::pressF1();
			parent::enterWarning();
			return false;
		}

		#success
		if(parent::$jda->screenCheck('This record is now updated in the file') || parent::$jda->screenWait('This record is now updated in the file')) {
			self::$formMsg = "{$box_code}: This record is now updated in the file";
			self::updateSyncStatus($box_code);
		}


		echo self::$formMsg;

		return true;
	}

	/*
	* Get all open boxes
	*/
	/*public function getBoxes()
	{
		$db = new pdoConnection();

		echo "\n Getting box codes from db \n";
		$sql = "SELECT b.box_code
				FROM wms_box_details bd
				INNER JOIN wms_box b ON b.box_code = bd.box_code
				WHERE bd.sync_status = 1 AND b.sync_status = 0
				GROUP BY bd.box_code";
		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['box_code'];
		}

		$db->close();

		return $result;
	}*/

	/*
	* Update ewms trasaction_to_jda sync_status
	*/
	/*private static function updateSyncStatus($box_code, $isError = FALSE)
	{
		$db = new pdoConnection();
		$date_today = date('Y-m-d H:i:s');

		$status = ($isError) ? parent::$errorFlag : parent::$successFlag;

		echo "\n Updating... \n";
		$sql 	= "UPDATE wms_box SET sync_status = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}'
					WHERE sync_status = 0 AND box_code = '{$box_code}'";
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
					WHERE sync_status = 0 AND module = 'Box Header' AND jda_action='Creation' AND reference = '{$reference}'";
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";
		$db->close();
	}

	/*
	* On done only via android
	*/
	public function enterUpToCartonHeaderMaintenance()
	{
		//TODO::checkvalues
		//TODO::how to know if error
		try {
			$title = "Palletizing Maintaining of Cartoon header \n";
			echo $title ;
			parent::checkRecoverJob();
			parent::checkJobOnProgress();
			parent::enterDistributionManagement();
			parent::enterWarehouseMaintenance();
			parent::enterCartonPalletLoadMaintenance();
			self::enterCartonHeaderMaintenance();

		} catch (Exception $e) {
			//send fail status
			echo 'Error: '. $e->getMessage();
		}

	}

	private static function syncPalletHeader($params)
	{
		$formattedString = "{$params['loadNo']}";
		$dbInstance = new pdoConnection(); //open db connection
		$dbInstance->daemon('palletizing_step2', $formattedString);
		$dbInstance->close();

		echo "Entered: Syncing pallet header.... \n";
	}

	public function logout($params = array())
	{
		parent::logout();
		self::syncPalletHeader($params);
	}

}

$db = new pdoConnection(); //open db connection

$jdaParams = array();
$jdaParams = array('module' => 'Box Header', 'jda_action' => 'Creation');

// format: php picklist.php {docNo} {$boxNo} {$palletNo} {$loadNo}
$execParams 			= array();
$execParams['loadNo'] 	= ((isset($argv[1]))? $argv[1] : NULL);

print_r($execParams);
if(isset($argv[1])) $jdaParams['reference'] = $execParams['loadNo'];

$getBoxes = $db->getJdaTransactionBoxHeader($jdaParams);
print_r($getBoxes);

if(! empty($getBoxes) )
{
	$palletizing = new palletizingStep1();
	$palletizing->enterUpToCartonHeaderMaintenance();
	// $getBoxes = $palletizing->getBoxes();
	foreach($getBoxes as $box) {
		$palletizing->save($box);
	}
	$palletizing->logout($execParams);
}
else {
	echo " \n No rows found!. Proceed to Pallet Header Creation\n";
	$formattedString = "{$execParams['loadNo']}";
	$db->daemon('palletizing_step2', $formattedString);
}
$db->close(); //close db connection