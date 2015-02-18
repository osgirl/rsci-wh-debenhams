<?php
include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class poReceiving extends jdaCustomClass 
{	
	private static $formMsg = "";
	public static $user = 'SYS';

	/*
	RECEIVING OF PO/ dock check of PO - DONE STATUS

	08
	02
	02
	receiver_no to enter: 20210
	tab
	enter
	tab for invoice number
	tab to received by: SYS
	tab invoice amount : 1
	tab
	tab
	tab
	tab
	tab
	tab
	tab
	tab
	tab
	tab to checked by: SYS
	ENTER
	F1
	F7
	*/

	public function __construct() {
		// parent::__construct();
		self::$formMsg = __METHOD__;
		parent::logError(self::$formMsg, __METHOD__);
		parent::login();
	}

	private static function enterMerchandising()
	{
		#enter merchandising
		parent::$jda->screenWait("Merchandise Receiving");
		parent::display(parent::$jda->screen,132);	
		parent::$jda->write5250(array(array("08",22,44)),ENTER,true);
		echo "Entered: Merchandise Receiving \n";
	}

	private static function enterStoreReceivingMenu()
	{
		parent::$jda->screenWait("Store Receiving Menu");
		parent::display(parent::$jda->screen,132);	
		parent::$jda->write5250(array(array("02",22,44)),ENTER,true);
		echo "Entered: Store Receiving Menu \n";
	}

	private static function enterDockReceipt()
	{
		parent::$jda->screenWait("Dock Receipt and Check-In");
		parent::display(parent::$jda->screen,132);	
		parent::$jda->write5250(array(array("02",22,44)),ENTER,true);
		echo "Entered: Dock Receipt and Check-In \n";
	}

	public function enterReceiverNumber($receiver_no)
	{
		parent::$jda->screenWait("Receiver Number");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%10d", $receiver_no),8,45);

		parent::$jda->write5250($formValues,ENTER,true);
		return self::checkReceiverNumber($receiver_no,__METHOD__);
		
	}

	private static function checkReceiverNumber($receiver_no,$source)
	{
		if(parent::$jda->screenCheck('This receiver number does not exist')) {
            $receiver_message='This receiver number does not exist';
			self::$formMsg = "{$receiver_no}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($receiver_no,"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Receiver is already being received by another user.')) {
            $receiver_message='Receiver is already being received by another user.';
            self::$formMsg = "{$receiver_no}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($receiver_no,"{$source}: {$receiver_message}", TRUE);
			return false;
		}
		//won't happen in the live environment
		if(parent::$jda->screenCheck('Receipt is already being processed through')) {
            $receiver_message="Receipt is already being processed through 'RF' or 'single'.";
            self::$formMsg = "{$receiver_no}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($receiver_no,"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('You cannot receive this receiver at this time')) {
            $receiver_message="You cannot receive this receiver at this time";
            self::$formMsg = "{$receiver_no}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($receiver_no,"{$source}: {$receiver_message}", TRUE);
			return false;
		}
		
		if(parent::$jda->screenCheck('This receiver has been detail received, cannot dock receive.')) {
            $receiver_message="This receiver has been detail received, cannot dock receive.";
            self::$formMsg = "{$receiver_no}: {$receiver_message}";
            parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($receiver_no,"{$source}: {$receiver_message}", TRUE);
			return false;
		}

		echo self::$formMsg;

		return true;
	}

	public function enterPOForm($receiver)
	{
		// $receiver_no = $receiver['reference'];
		parent::$jda->screenWait("Date Received");
		parent::display(parent::$jda->screen,132);
		self::enterPoStoreReceipt($receiver);
	}

	private static function enterPoStoreReceipt($receiver) {
		$invoice_amt = 1; //set 1 for now
		$formValues = array();//values to enter to form
		$formValues[] = array(self::$user,12,69);  //enter receive by
		$formValues[] = array(sprintf("%20d", $invoice_amt),13,20); //enter invoice amount
		$formValues[] = array(self::$user,17,72);  //enter checked by
		parent::$jda->write5250($formValues,ENTER,true);
		echo "Entered: Purchase Order Store Receipt  \n";
		self::checkReceiverLanding($receiver);
	}

	private static function checkReceiverLanding($receiver) {
		parent::$jda->screenWait("Receiver Number");
		parent::display(parent::$jda->screen,132);
		//when it lands here we can now assume that the transaction was a success
		//need more test here
		self::updateSyncStatus($receiver);
	}

	/*public function getReceiverNo() {
		$db = new pdoConnection();

		echo "\n Getting receiver no from db \n";
		$sql 	= "SELECT receiver_no reference
					FROM wms_transactions_to_jda 
                    INNER JOIN wms_purchase_order_lists ON reference = purchase_order_no
					WHERE module = 'Purchase Order' AND jda_action='Receiving' AND sync_status = 0";
		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['reference'];
		}

		$db->close();

		return $result;
	}*/

	/*
	* Update ewms trasaction_to_jda sync_status
	*/
	private static function updateSyncStatus($reference,$error_message=null, $isError = FALSE) {
		$db = new pdoConnection();
		$date_today = date('Y-m-d H:i:s');

		$status = ($isError) ? parent::$errorFlag : parent::$successFlag;

		echo "\n Getting receiver no from db \n";
		$sql 	= "UPDATE wms_transactions_to_jda 
					SET sync_status = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}', error_message = '{$error_message}'
					WHERE sync_status = 0 AND module = 'Purchase Order' AND jda_action='Receiving' AND reference = (SELECT purchase_order_no FROM wms_purchase_order_lists po WHERE po.receiver_no = {$reference})";
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";
		$db->close();
	}

	/*
	* On done only via android
	*/
	public function enterUpToDockReceipt()
	{	
		//TODO::checkvalues
		//TODO::how to know if error
		try {
			$title = "Receiving PO \n";
			echo $title ;
			parent::checkRecoverJob();
			parent::checkJobOnProgress();
			self::enterMerchandising();
			self::enterStoreReceivingMenu();
			self::enterDockReceipt();
		} catch (Exception $e) {
			//send fail status
			echo 'Error: '. $e->getMessage();
		}
		
	}

	private static function syncPOClosing($params)
	{
		$formattedString = "{$params['poNo']}";
		$dbInstance = new pdoConnection(); //open db connection
		$dbInstance->daemon('close_po', $formattedString);
		$dbInstance->close();

		echo "Entered: Syncing purchase order closing.... \n";
	}

	public function logout($params = array())
	{	
		parent::logout();
		self::syncPOClosing($params);
	}
	
}

$db = new pdoConnection(); //open db connection

$params = array();
$params = array('module' => 'Purchase Order', 'jda_action' => 'Receiving');

$execParams 			= array();
$execParams['poNo'] 	= ((isset($argv[1]))? $argv[1] : NULL);
if($argv[1]) $params['reference'] = $execParams['poNo'];

$poNos = $db->getJdaTransaction($params);

if(! empty($poNos) ) 
{
	$receiver_nos = $db->getReceiverNo($poNos);
	print_r($receiver_nos);
	if(! empty($receiver_nos) )
	{
		$receivePO = new poReceiving();
		$receivePO->enterUpToDockReceipt();
		foreach($receiver_nos as $receiver) 
		{
			$validate = $receivePO->enterReceiverNumber($receiver);
			if($validate) $receivePO->enterPOForm($receiver);
		}
		$receivePO->logout($execParams);
	}
	else {
		echo " \n No receiver_nos found!. \n";	
	}
}
else {
	echo " \n No rows found!. Proceed to Closing of PO...\n";
	$formattedString = "{$execParams['poNo']}";
	$db->daemon('close_po', $formattedString);
}
$db->close(); //close db connection
/*
$receivePO = new poReceiving();
$receiver_nos = $receivePO->getReceiverNo();
print_r($receiver_nos);
if(! empty($receiver_nos) ) 
{
	$receivePO->enterUpToDockReceipt();
	foreach($receiver_nos as $receiver) {
		$validate = $receivePO->enterReceiverNumber($receiver);
		if($validate) $receivePO->enterPOForm($receiver);
	}
}
else {
	echo " \n No rows found!. \n";
}
$receivePO->logout();
*/