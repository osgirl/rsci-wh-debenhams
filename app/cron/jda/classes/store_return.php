<?php
include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class storeReturn extends jdaCustomClass
{
	private static $formMsg = "";
	public static $user = 'SYS';

	/*
	STORE RETURN 

	NOTES
	>first attempt: all rows must have received qty (bug)
	>what happens to transferred batch (daemon at syncclosing)
	>how to detect sku that don't exist in jda
	>pagination
	>invalid slot error

	*master menu
	09
	*transfers/return to vendor
	01
	*transfer management
	17
	*transfer receipts entry
	6097
	*transfer receipts menu
	1
	*transfer receipt maintenance-sku
	TAB
	MA1C1L1

	*if sku not exist : add
	f9
	enter sku
	tab
	>>enter quantity
	f7

	12 max tab
	pg dn (roll up)
	>pack received quantity
	f10
	*
	enter

	check
	09
	01
	29
	06

	check slot 
	13
	24
	03
	*/

	public function __construct() {
		// parent::__construct();
		self::$formMsg = __METHOD__;
		parent::logError(self::$formMsg, __METHOD__);
		parent::login();
	}

	private static function enterTransfers()
	{
		#enter merchandising
		parent::$jda->screenWait("Transfers/Return to Vendor");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("09",22,44)),ENTER,true);
		echo "Entered: Transfers/Return to Vendor \n";
	}

	private static function enterTransferManagement()
	{
		parent::$jda->screenWait("Transfer Management");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("01",22,44)),ENTER,true);
		echo "Entered: Transfer Management \n";
	}

	private static function enterReceiveTransfer()
	{
		parent::$jda->screenWait("Receive Transfers");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("17",22,44)),ENTER,true);
		echo "Entered: Receive Transfers \n";
	}

	public function enterTransferNumber($transfer_no)
	{
		parent::$jda->screenWait("Transfer Number");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%2d", $transfer_no),10,44);

		parent::$jda->write5250($formValues,ENTER,true);
		return self::enterSKUNumber($transfer_no,__METHOD__);

	}

	public function enterSKUNumber($transfer_no,$source)
	{
		parent::$jda->screenWait("SKU Number");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("1",19,52)),ENTER,true);
		echo "Entered: SKU Number \n";
		return self::checkTransferNumber($transfer_no,$source);
	}

	private static function checkTransferNumber($transfer_no,$source)
	{
		if(parent::$jda->screenCheck('Transfer batch not in shipped status.')) {
            $transfer_message="Transfer batch not in shipped status.";
            self::$formMsg = "{$transfer_no}: {$transfer_message}";
            parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($transfer_no,"{$source}: {$transfer_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Transfer batch invalid')) {
            $transfer_message="Transfer batch invalid.";
            self::$formMsg = "{$transfer_no}: {$transfer_message}";
            parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($transfer_no,"{$source}: {$transfer_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Transfer is being modified by another user.')) {
            $transfer_message='Transfer is being modified by another user.';
            self::$formMsg = "{$transfer_no}: {$transfer_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($transfer_no,"{$source}: {$transfer_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Invalid slot - does not exist.')) {
            $transfer_message='Invalid slot - does not exist.';
            self::$formMsg = "{$transfer_no}: {$transfer_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($transfer_no,"{$source}: {$transfer_message}", TRUE);
			return false;
		}
		echo self::$formMsg;

		return true;
	}

	public function enterSOForm($transferer, $slot_code, $transfer_nos)
	{
		// $receiver_no = $receiver['reference'];
		parent::$jda->screenWait("Transfer Receipt Maintenance-SKU");
		parent::display(parent::$jda->screen,132);
		self::enterSoTransferReceipt($transferer, $slot_code, $transfer_nos);
	}
	private static function enterSoTransferReceipt($transferer, $slot_code, $transfer_nos) {
		$invoice_amt = 1; //set 1 for now
		$formValues = array();//values to enter to form //enter slot
		$column 	= 78;
		$row 		= 10;
		$currentpage=1;
		// $qtyMoved 	= array(1);
		$formValues = array();
		$formValues[] = array(sprintf("%-8s", $slot_code),7,12);
		//coordinates start on 37/9
		for ($i=0; $i < count($transfer_nos); $i++) {
			if($i>0 && $i%12==0){
				parent::$jda->write5250($formValues,F7,true);
				parent::$jda->write5250(NULL,F7,true);

				if(parent::$jda->screenCheck('All Recieved Quantities are ZERO.')) {
					parent::$jda->write5250(NULL,F1,true);
					parent::display(parent::$jda->screen,132);
				}				
				$formValues = array();

				parent::$jda->write5250(NULL,ROLLUP,true);
				$row = 10;
				$currentpage++;
			}
			echo "\n page is: {$currentpage} \n";
			echo "\n value of row is: {$row} \n";
			echo "value of quantity received is: {$transfer_nos[$i]['received_qty']} \n";
			$formValues[] = array(sprintf("%10d", $transfer_nos[$i]['received_qty']),$row,$column); //enter moved_qty
			$row++;
		}

		parent::$jda->write5250($formValues,F10,true);
		parent::$jda->write5250(NULL,F10,true);

		if(parent::$jda->screenCheck("This is a WARNING")) {
			parent::$jda->write5250(NULL,F1,true);
			parent::$jda->write5250(NULL,F10,true);
			parent::display(parent::$jda->screen,132);
		}

		$validate = self::checkTransferNumber($transferer,__METHOD__);

		if ($validate)
		{
			echo "Entered: Store Return Transfer Receipt  \n";
			self::checkTransferLanding($transferer,__METHOD__);
		}
	}

	private static function checkTransferLanding($transferer,$source) {
		if(parent::$jda->screenCheck('This job has been placed on a batch Job Queue')) {
			parent::display(parent::$jda->screen,132);
			parent::$jda->write5250(NULL,ENTER,true);
			self::updateSyncStatus($transferer);
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
		$sql 	= "UPDATE wms_transactions_to_jda
					SET sync_status = {$status}, updated_at = '{$date_today}', jda_sync_date = '{$date_today}', error_message = '{$error_message}'
					WHERE sync_status = 0 AND module = 'Store Return' AND jda_action='Returning' AND reference = (SELECT so_no FROM wms_store_return WHERE so_no = {$reference})";
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";
		$db->close();
	}

	/*
	* On done only via android
	*/
	public function enterTransferReceipt()
	{
		//TODO::checkvalues
		//TODO::how to know if error
		try {
			$title = "Returning SO \n";
			echo $title ;
			parent::checkRecoverJob();
			parent::checkJobOnProgress();
			self::enterTransfers();
			self::enterTransferManagement();
			self::enterReceiveTransfer();
		} catch (Exception $e) {
			//send fail status
			echo 'Error: '. $e->getMessage();
		}

	}

	private static function syncSOClosing($params)
	{
		$formattedString = "{$params['soNo']}";
		// $dbInstance = new pdoConnection(); //open db connection
		// $dbInstance->daemon('close_po', $formattedString);
		// $dbInstance->close();

		echo "Entered: Syncing store return closing.... \n";
	}

	public function logout($params = array())
	{
		parent::logout();
		self::syncSOClosing($params);
	}

}

$db = new pdoConnection(); //open db connection

$params = array();
$params = array('module' => 'Store Return', 'jda_action' => 'Returning');

$execParams 			= array();
$execParams['soNo'] 	= ((isset($argv[1]))? $argv[1] : NULL);
if($argv[1]) $params['reference'] = $execParams['soNo'];

$soNos = $db->getJdaTransaction($params);

if(! empty($soNos) )
{
	$transfer_nos = $db->getTransferNo($soNos);
	print_r($transfer_nos);
	if(! empty($transfer_nos) )
	{
		$returnSO = new storeReturn();
		$returnSO->enterTransferReceipt();
		// foreach($transfer_nos as $transfer_no)
		// {
			$transferer = $transfer_nos[0]['transfer_no'];
			$slot_code = $transfer_nos[0]['slot_code'];

			$validate = $returnSO->enterTransferNumber($transferer);

			if($validate) $returnSO->enterSOForm($transferer, $slot_code,$transfer_nos);
		// }
		$returnSO->logout($execParams);
	}
	else {
		echo " \n No receiver_nos found!. \n";
	}
}

$db->close(); //close db connection