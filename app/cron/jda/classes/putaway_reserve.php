<?php

include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class putawayReserve extends jdaCustomClass 
{	
	private static $formMsg = "";
	public static $user = 'SYS';
	public static $warehouseNo = "9005 ";
	public static $fromSlot = "RZ000001";
	/*

13
04
03
03
Warehouse number: 9005
space bar - to delete the rest of the number
enter sku number: 900483
tab
SYS
From slot: RZ000001
to slot: PCK00001
Quantity: 4
END
F7
F1
F7

	*/
	public function __construct() {
		// parent::__construct();
		parent::login();
	}

	private static function enterGeneralMaintenance()
	{
		parent::$jda->screenWait("Move And General Maintenance");
		parent::display(parent::$jda->screen,132);	
		parent::$jda->write5250(array(array("03",22,44)),ENTER,true);
		echo "Entered: Move And General Maintenance \n";
	}

	private static function enterManualMoves()
	{
		parent::$jda->screenWait("Manual Moves");
		parent::display(parent::$jda->screen,132);	
		parent::$jda->write5250(array(array("03",22,44)),ENTER,true);
		echo "Entered: Manual Moves \n";
	}

	public function enterInventoryMovement()
	{
		parent::$jda->screenWait("Vendor/Vendor");
		parent::display(parent::$jda->screen,132);
		echo "Entered: Inventory Movement \n";
	}

	public function enterForm($data) {
		print_r($data);
		$sku = $data['sku'];
		$quantity = $data['quantity'];
		$toSlot = $data['toSlot'];
		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%5s", self::$warehouseNo), 4, 32);// enter warehouse number
		$formValues[] = array(sprintf("%9d", $sku), 6, 32);// enter sku
		$formValues[] = array(self::$user, 11, 32);// enter clerk initial
		$formValues[] = array(sprintf("%8s", self::$fromSlot), 13, 32);// enter from slot
		// $formValues[] = array(sprintf("%8s", $toSlot), 14, 32);// enter to slot
		$formValues[] = array($toSlot, 14, 32);// enter to slot
		$formValues[] = array(sprintf("%11d", $quantity), 15, 32);// enter quantity
		parent::$jda->write5250($formValues,F7,true);

		self::checkResponse($data);
	}

	private static function checkResponse($data = array()) 
	{
		# error
		if(parent::$jda->screenCheck('Please enter a valid warehouse number')) {
			self::$formMsg = "{self::$warehouseNo}: Please enter a valid warehouse number";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data, TRUE);
		}

		if(parent::$jda->screenCheck("Invalid 'sku' entered")) {
			self::$formMsg = "{$data['sku']}: Invalid 'sku' entered";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data, TRUE);
		}

		if(parent::$jda->screenCheck("Clerk is not valid")) {
			self::$formMsg = "{self::$warehouseNo}: Clerk is not valid";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data, TRUE);
		}

		if(parent::$jda->screenCheck("'From slot' or 'new primary slot' must be entered")) {
			self::$formMsg = "{self::$fromSlot}: From slot or new primary slot must be entered";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data, TRUE);
		}

		if(parent::$jda->screenCheck("To slot or new primary slot must be entered")) {
			self::$formMsg = "{$data['toSlot']}: To slot or new primary slot must be entered";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data, TRUE);
		}

		if(parent::$jda->screenCheck("Slot not valid for this warehouse")) {
			self::$formMsg = "{$data['toSlot']}: Slot not valid for this warehouse";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data, TRUE);
		}

		if(parent::$jda->screenCheck("Use of decimals incorrect or too many numbers entered")) {
			self::$formMsg = "{$data['quantity']}: Use of decimals incorrect or too many numbers entered";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data, TRUE);
		}
		
		if(parent::$jda->screenCheck('Quantity requested to move exceeds quantity available')) {
			self::$formMsg = "{$data['sku']}: Quantity requested to move exceeds quantity available";	
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data, TRUE);
		}

		if(parent::$jda->screenCheck('Cannot move between two identical slots')) {
			self::$formMsg = "{$data['sku']}: Cannot move between two identical slots";	
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($data, TRUE);
		}

		echo self::$formMsg;
		# end error

		# upon success
		if(parent::$jda->screenCheck('moved from RZ000001')) {
			echo "\n Successfully added to PCK00001";
			self::updateSyncStatus($data);
		}

		# when all forms are ok but the qty entered is 0
		if(parent::$jda->screenCheck('has been changed to')) {
			echo "\n Primary slot for SKU {$data['sku']} has been changed to";
		}
		
	}

	/*public function getSkus() {
		$db = new pdoConnection();

		echo "\n Getting data from slot_details \n";
		$sql 	= "SELECT slot_id, wms_product_lists.sku AS sku, quantity FROM wms_slot_details 
					INNER JOIN wms_product_lists ON wms_slot_details.sku = wms_product_lists.upc
					WHERE sync_status = 0";
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
	private static function updateSyncStatus($data = array(), $isError = FALSE) {
		$db = new pdoConnection();
		$date_today = date('Y-m-d H:i:s');
		$sku = $data['sku'];
		$slot = $data['toSlot'];

		$status = ($isError) ? parent::$errorFlag : parent::$successFlag;

		echo "\n Getting receiver no from db \n";
		/*$sql 	= "UPDATE wms_slot_details 
					SET sync_status = 1, updated_at = '{$date_today}'
					WHERE sku = {$sku} AND slot_id = '{$slot}'";*/
		$sql = "UPDATE wms_slot_details sd
				SET sync_status = {$status}, updated_at = '{$date_today}'
				WHERE sd.sku = (SELECT upc FROM wms_product_lists pl WHERE pl.sku = {$sku}) AND slot_id = '{$slot}'";
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
			$title = "Putaway to Reserve \n";
			echo $title;
			parent::checkRecoverJob();
			parent::checkJobOnProgress();
			parent::enterDistributionManagement();
			parent::enterWarehouseMaintenance();
			self::enterGeneralMaintenance();
			self::enterManualMoves();
			
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
$skus = $db->getPutawaySkus();
$db->close(); //close db connection

if(! empty($skus) ) 
{
	$putawayReserve = new putawayReserve();
	$putawayReserve->enterUpToManualMoves();
	$params = array();
	foreach($skus as $value) 
	{
		$params = array(
				'toSlot' => $value['slot_id'],
				'sku'	 => $value['sku'],
				'quantity' => $value['quantity']
			);
		$putawayReserve->enterInventoryMovement();
		$putawayReserve->enterForm($params);
	}
	$putawayReserve->logout();
}
else {
	echo " \n No rows found!. \n";
}

$putawayReserve = new putawayReserve();

$putawayReserve->enterUpToManualMoves();
$skus = $putawayReserve->getSkus();
$params = array();

if(! empty($skus) ) 
{
	foreach($skus as $value) 
	{
		$params = array(
				'toSlot' => $value['slot_id'],
				'sku'	 => $value['sku'],
				'quantity' => $value['quantity']
			);
		$putawayReserve->enterInventoryMovement();
		$putawayReserve->enterForm($params);
	}
}

$putawayReserve->logout();

