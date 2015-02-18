<?php

include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class storeReceiving extends jdaCustomClass 
{	
	private static $formMsg = "";
	private static $weight = 1;
	private static $soStatus = 3; //for closed so
/*
store receiving

	09
	01
	20
	01
	Enter Carton id: CTNXXX20
	press ENTER
	per item (ASCENDING ORDER BY SKU) (loop)
	F7
	F10
	press ENTER


*/

	public function __construct() {
		// parent::__construct();
		parent::login();
	}

	private static function enterTransferReturnToVendor()
	{
		parent::$jda->screenWait("Transfers/Return");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("09",22,44)),ENTER,true);
		echo "Entered: Transfer or Return to Vendor \n";	
	}

	private static function enterTransferManagement() 
	{
		parent::$jda->screenWait("Transfer Management");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("01",22,44)),ENTER,true);
		echo "Entered: Transfer Management \n";	
	}

	private static function enterReceiveTransferCartons()
	{
		parent::$jda->screenWait("Receive Transfer Cartons");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("20",22,44)),ENTER,true);
		echo "Entered: Receive Transfer Cartons/Loads \n";	
	}

	public function enterCartonReceiving()
	{
		parent::$jda->screenWait("Carton Receiving");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("01",22,44)),ENTER,true);
		echo "Entered: Carton Receiving \n";	
	}

	public function enterCartonId($box_code, $store_code)
	{
		parent::$jda->screenWait("Enter Carton Id");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		// $formValues[] = array(sprintf("%9s", $box_code),10,40); //enter carton id
		$formValues[] = array($box_code,10,40); //enter carton id
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: Carton Id \n";

		return self::checkResponse($box_code, $store_code,__METHOD__);
	}

	public function enterForm($box_code, $store_code)
	{
		parent::$jda->screenWait("ID Number");
		parent::display(parent::$jda->screen,132);

		$column 	= 9;
		$row 		= 4;
		$qtyDelivered = self::getQtyDelivered($box_code, $store_code);
		$formValues = array();
		//coordinates start on 37/9
		for ($i=0; $i < count($qtyDelivered); $i++) {
			$new_col = ($i + $column);
			echo "\n value of new_col is: {$new_col} \n";
			echo "value of quantity moved is: {$qtyDelivered[$i]} \n";
			$formValues[] = array(sprintf("%11d", $qtyDelivered[$i]),$new_col,$row); //enter moved_qty
		}
		
		// echo $i;
		// if(parent::$jda->screenCheck('F7=Accept') && $i == count($qtyDelivered)) {
		parent::$jda->write5250($formValues,ENTER,true);
		parent::$jda->write5250($formValues,F7,true); 
		echo "Entered: Detail Form \n";
		return self::checkResponse($box_code, $store_code,__METHOD__);
		// }
	}

	private static function jobQueue($box_code, $store_code)
	{
		parent::$jda->screenWait("batch Job Queue");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(NULL,ENTER,true);
		echo "Entered: Job Queue \n";

		self::updateSyncStatus($box_code, FALSE, $store_code);// update status
	}

	public function save($box_code, $store_code = NULL)
	{
		//WHS0151: Carton added to the submit processing selections
		if(parent::$jda->screenCheck('WHS0151'))
		{
			parent::pressF10();
			self::jobQueue($box_code, $store_code);
		}
	}
	

	private static function checkResponse($box_code, $store_code,$source)
	{
		# error
		if(parent::$jda->screenCheck('Carton ID must be entered')) {
            $receiver_message="Carton ID must be entered";
			self::$formMsg = "{$box_code}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($box_code,"{$source}: {$receiver_message}", TRUE, $store_code);
			return false;
		}

		if(parent::$jda->screenCheck('Carton ID not valid')) {
            $receiver_message="Carton ID not valid";
            self::$formMsg = "{$box_code}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($box_code,"{$source}: {$receiver_message}", TRUE, $store_code);
			return false;
		}

		if(parent::$jda->screenCheck('Warning: The received quantity is less than the shipped quantity')) {
            $receiver_message="Warning: The received quantity is less than the shipped quantity";
            self::$formMsg = "{$box_code}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($box_code,"{$source}: {$receiver_message}", TRUE, $store_code);
			return false;
		}

		if(parent::$jda->screenCheck('The received quantity is greater than the shipped')) {
            $receiver_message="The received quantity is greater than the shipped quantity";
            self::$formMsg = "{$box_code}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($box_code,"{$source}: {$receiver_message}", TRUE, $store_code);
			return false;
		}

		if(parent::$jda->screenCheck('The carton status is not correct for receiving')) {
            $receiver_message="The carton status is not correct for receiving";
            self::$formMsg = "{$box_code}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($box_code,"{$source}: {$receiver_message}", TRUE, $store_code);
			return false;
		}

		#success
		if(parent::$jda->screenCheck('Carton added to the submit processing selections')) {
			self::$formMsg = "{$box_code}: Carton added to the submit processing selections";
		}

		

		echo self::$formMsg;
		return true;
	}

	/*
	* Get boxes
	*/
	/*public function getBoxes() 
	{
		$db = new pdoConnection();

		echo "\n Getting load_code from db \n";
		$sql = "SELECT DISTINCT b.box_code, b.store_code
					FROM wms_store_order so
					INNER JOIN wms_load_details ld ON ld.load_code = so.load_code
					INNER JOIN wms_pallet_details pd ON ld.pallet_code = pd.pallet_code
                    INNER JOIN wms_box b ON b.box_code = pd.box_code AND so.store_code = b.store_code
					WHERE so_status = ". self::$soStatus ." AND so.sync_status = 0
					GROUP BY b.box_code, so.load_code";
		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			// $result[] = $value['box_code'];
			$result[] = $value;
		}

		$db->close();

		return $result;
	}*/

	/*
	* Get all skus per box
	*/
	public function getQtyDelivered($box_code, $store_code) 
	{
		$db = new pdoConnection();

		echo "\n Getting load_code from db \n";
		/*$sql 	= "SELECT so.id, so.so_no, so.store_code, pd.id pick_detail_id, bd.box_code, pd.sku, SUM(so_detail.delivered_qty) delivered_qty
				    FROM wms_store_order so 
				    RIGHT JOIN wms_store_order_detail so_detail ON so_detail.so_no = so.so_no
				    LEFT JOIN wms_picklist_details pd ON pd.so_no = so.so_no 
				    INNER JOIN wms_box_details bd ON bd.picklist_detail_id = pd.id
				    INNER JOIN wms_product_lists prod ON prod.upc = so_detail.sku
				    WHERE so.so_status = ". self::$soStatus ." AND so.sync_status = 0 AND bd.box_code = '{$box_code}'
				    GROUP BY pd.sku, so.store_code
				    ORDER BY prod.sku ASC";*/
		/*$sql 	= "SELECT so.id, so.so_no, so.store_code, so_detail.sku, so_detail.delivered_qty
					, (SELECT MIN(box_code) FROM wms_picklist_details pd RIGHT JOIN wms_box_details bd ON bd.picklist_detail_id = pd.id WHERE pd.so_no = so_detail.so_no AND box_code = '{$box_code}' GROUP BY box_code) AS box
					FROM wms_store_order_detail so_detail 
					INNER JOIN wms_store_order so ON so.so_no = so_detail.so_no AND so.so_status = ". self::$soStatus ." AND so.sync_status = 0
					INNER JOIN wms_product_lists prod ON prod.upc = so_detail.sku
					ORDER BY prod.sku ASC";*/
		$sql 	= "SELECT so.id, so.so_no, so.store_code, prod.sku, pd.box_code, so.load_code, so_detail.sku, so_detail.delivered_qty
				    FROM wms_store_order so 
				    RIGHT JOIN wms_store_order_detail so_detail ON so_detail.so_no = so.so_no
                    INNER JOIN wms_product_lists prod ON prod.upc = so_detail.sku
				    LEFT JOIN wms_load_details ld ON ld.load_code = so.load_code
                    LEFT JOIN wms_pallet_details pd ON pd.pallet_code = ld.pallet_code
				    WHERE so.so_status = ". self::$soStatus ." AND so.sync_status = 0 AND box_code = '{$box_code}' AND store_code = {$store_code}
				    ORDER BY prod.sku ASC";
		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['delivered_qty'];
		}

		$db->close();

		return $result;
	}

	/*
	* Update batch wms_store_order so_status to 3 (close)
	*/
	private static function updateSyncStatus($box_code,$error_message=null, $isError = FALSE, $store_code = NULL)
	{
		$db = new pdoConnection();
		$date_today = date('Y-m-d H:i:s');

		$status = ($isError) ? parent::$errorFlag : parent::$successFlag;

		echo "\n Updating... \n";

		$sql = "UPDATE wms_store_order SET wms_store_order.sync_status = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}', error_message = '{$error_message}'
				WHERE wms_store_order.sync_status = 0 AND load_code = (SELECT load_code FROM `wms_pallet_details` pd
									INNER JOIN wms_load_details ld ON ld.pallet_code = pd.pallet_code
									WHERE box_code = '{$box_code}') AND store_code = {$store_code}";
		
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";

		$db->close();
	}

	/*
	* On done only via android
	*/
	public function enterUpToReceiveTransferCartons()
	{	
		//TODO::checkvalues
		//TODO::how to know if error
		try {
			$title = "Shipping \n";
			echo $title ;
			parent::checkRecoverJob();
			parent::checkJobOnProgress();
			self::enterTransferReturnToVendor();
			self::enterTransferManagement();
			self::enterReceiveTransferCartons();
			
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
$getBoxes = $db->getSOBoxes($params);
$db->close(); //close db connection

if(! empty($getBoxes) ) 
{
	$store = new storeReceiving();
	$store->enterUpToReceiveTransferCartons();

	// $getBoxes = $store->getBoxes();
	foreach($getBoxes as $box) 
	{
		$store->enterCartonReceiving();
		$validate = $store->enterCartonId($box['box_code'], $box['store_code']);
		if($validate)
		{
			$validateDetail = $store->enterForm($box['box_code'], $box['store_code']);
			if($validateDetail) $store->save($box['box_code'], $box['store_code']);
		} 
	}
	$store->logout();
}
else {
	echo " \n No rows found!. \n";
}

