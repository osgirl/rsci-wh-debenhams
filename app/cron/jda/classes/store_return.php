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

	check bg process
	sr header -1 status
	delete in sr detail
	check logs for output

	data
	6288
	6289
	6290
	6295
	6296
	6303
	6304
	6305
	6306
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
		// $formValues[] = array(sprintf("%2d", $transfer_no),10,44);
		$formValues[] = array(sprintf("%-8d", $transfer_no),10,44);

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

		if(parent::$jda->screenCheck('Transfer is carton-manifested-use load receiving.')) {
            $transfer_message='Transfer is carton-manifested-use load receiving.';
            self::$formMsg = "{$transfer_no}: {$transfer_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($transfer_no,"{$source}: {$transfer_message}", TRUE);
			return false;
		}

		if(parent::$jda->screenCheck('Invalid slot')) {
            $transfer_message='Invalid slot';
            self::$formMsg = "{$transfer_no}: {$transfer_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($transfer_no,"{$source}: {$transfer_message}", TRUE);
			return false;
		}
		echo self::$formMsg;

		return true;
	}

	public function enterSOForm($transferer, $slot_code, $transfer_nos, $not_in_transfer_details)
	{
		parent::$jda->screenWait("Transfer Receipt Maintenance-SKU");
		parent::display(parent::$jda->screen,132);
		echo "Entered: Transfer Receipt Maintenance-SKU \n";
		self::enterSoTransferReceipt($transferer, $slot_code, $transfer_nos, $not_in_transfer_details);
	}

	private static function enterSoTransferReceipt($transferer, $slot_code, $transfer_nos, $not_in_transfer_details) {
		$column 	= 10;
		$row 		= 78;
		$limit      = 12;
		$total      = count($transfer_nos);
		$offset     = 0;
		$count      = ceil($total / $limit);
		$formValues = array();

		parent::$jda->write5250(array(array(sprintf("%-8s", $slot_code),7,12)),F7, true);//enter sku
		self::showWarning($formValues);
		$validate = self::checkTransferNumber($transferer,__METHOD__);

		// if slot is valid continue transaction
		if ($validate)
		{
			self::captureWarning();
			self::enterTransferReceiptMaintenanceSkuAgain($transferer);

			//enter not in transfer quantities
			if(is_array($not_in_transfer_details) && !empty($not_in_transfer_details)) {
				echo "\n Has not in transfer data \n";
				self::enterAddItems();

				if(parent::$jda->screenCheck("Add Items To Transfer"))
				{
					foreach ($not_in_transfer_details as $not_in_transfer_detail) {
						$formValues = array();
						$formValues[] = array(sprintf("%10d", $not_in_transfer_detail['sku']),16,38);
						$formValues[] = array(sprintf("%10d", $not_in_transfer_detail['received_qty']),19,34);
						parent::$jda->write5250($formValues,F7,true);
						parent::display(parent::$jda->screen,132);
					}
					parent::$jda->set_pos(21,24);
					parent::$jda->write5250(null,F1,true);
					parent::display(parent::$jda->screen,132);
					echo "\n Entered not in transfer \n";
				}

				$tries=0;
				while($tries++ < 5 && parent::$jda->screenCheck("Add Items To Transfer"))
				{
					echo "\n Found Add Items To Transfer pressed F1 \n";
					parent::$jda->write5250(null,F1,true);
				}

				$tries3=0;
				while($tries3++ < 5 && parent::$jda->screenWait("You have requested to Exit")){
					echo "\n Found *********** WARNING *********** pressed F12 & tries: {$tries3} \n";
					parent::$jda->set_pos(17,24);
					parent::$jda->write5250(null,F12,true);
				}
			}

			// paginate
			//enter first default quantites
			// if(! parent::$jda->screenCheck("Add Items To Transfer")) {

			while($offset < $count) {
				echo "\n Count: {$count} \n";
				$new = $offset;

				if ($new !== 0) {
					$new = $new * $limit;

					for($i=0; $i < $offset; $i++)
					{
						echo "\nCounter of i is: {$offset} \n";
						echo "\nEntered ROLLUP: Page: {$offset} with offset of: {$new} and row {$row} \n";
						parent::$jda->write5250(null,ROLLUP,true);
						parent::display(parent::$jda->screen,132);
					}
				}
				$page = array_slice( $transfer_nos, $new, $limit );
				$formValues = array();
				foreach ($page as $key => $value) {
					$new_column = $key + $column;
					echo "\n value of new_col is: {$new_column} \n";
					echo "value of transfer_nos is: {$value['received_qty']} \n";
					$formValues[] = array(sprintf("%10d", $value['received_qty']),$new_column,$row); //enter qty_delivered

				}
				parent::display(parent::$jda->screen,132);
				self::showWarning($formValues);
				self::captureWarning();
				self::reenterValues($formValues, $offset);
				self::enterTransferReceiptMaintenanceSkuAgain($transferer);

				$offset++;
			}
			parent::display(parent::$jda->screen,132);

			self::closeTransaction($transferer);
			// }
		}
	}

	private static function enterAddItems() {
		parent::$jda->write5250(null,F9,true);
	}

	private static function closeTransaction($transferer)
	{
		parent::$jda->set_pos(27,80);
		parent::$jda->write5250(null,F10,true);

		if (parent::$jda->screenCheck("All Recieved Quantities are ZERO.")){
			parent::$jda->write5250(null,F1,true);
			parent::$jda->write5250(null,F10,true);
			echo "\nF10 pressed \n";
			parent::display(parent::$jda->screen,132);
		}

		echo "Enter closing of store return \n";
		$tries3=0;
		while($tries3++ < 5 && !parent::$jda->screenWait("This job has been placed on a batch Job Queue")){
			echo "\n Unable to find This job has been placed on a batch Job Queue & tries: {$tries3} \n";
			parent::$jda->set_pos(27,80);
			parent::$jda->write5250(null,F10,true);
		}
		self::checkTransferLanding($transferer,__METHOD__);
	}

	private static function rescroll($count) {
		echo "\nEntered rescroll parameter count values is : {$count} \n";

		for($i=0; $i < $count; $i++)
		{
			echo "\nCounter of i is: {$i} \n";
			echo "\nEntered ROLLUP: Page: {$i} with offset of: {$count} \n";
			parent::$jda->write5250(null,ROLLUP,true);
			parent::display(parent::$jda->screen,132);
		}

	}

	private static function reenterValues($formValues, $offset) {
		if (! parent::$jda->screenWait("Transfer Number") && parent::$jda->screenCheck("F5=Msg")) {
			echo "\n Found F5=Msg!!! reenter values: {$tries} \n";
			self::rescroll($offset);
			parent::$jda->write5250($formValues,F7,true);
			parent::display(parent::$jda->screen,132);
		}
	}

	private static function showWarning($formValues) {
		$tries=0;
		while($tries++ < 5 && !parent::$jda->screenWait("All Recieved Quantities are ZERO."))
		{
			echo "\nF1 not yet processed pressed F7 & tries: {$tries} \n";
			if (! parent::$jda->screenCheck("Transfer Number")) {
				parent::$jda->write5250($formValues,F7,true); // doesn't affect if we press multiple F7 key
				parent::display(parent::$jda->screen,132);
			} else {
				break;
			}
		}
	}

	private static function captureWarning() {
		if (! parent::$jda->screenWait("Transfer Number")) {
			parent::$jda->screenCheck("This is a WARNING");
			echo "\n Found This is a WARNING!!! pressed F1 to exit tries: {$tries} \n";
			parent::$jda->write5250(null,F1,true);
			parent::display(parent::$jda->screen,132);
		}
	}

	private static function enterTransferReceiptMaintenanceSkuAgain($transfer_no) {

		if (parent::$jda->screenWait("Transfer Number")) {
			parent::display(parent::$jda->screen,132);

			$formValues = array();//values to enter to form
			$formValues[] = array(sprintf("%-8d", $transfer_no),10,44);

			parent::$jda->write5250($formValues,ENTER,true);
			parent::$jda->screenWait("Transfer Receipt Maintenance-SKU");
			echo "\n Entered Transfer Receipt Maintenance-SKU again \n";
			parent::display(parent::$jda->screen,132);
		}
	}

	private static function checkTransferLanding($transferer,$source) {
		if(parent::$jda->screenWait('This job has been placed on a batch Job Queue')) {
			echo 'This job has been placed on a batch Job Queue';
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
	foreach ($soNos as $soNo) {
		$transfer_details = $db->getTransferNo($soNo);
		$not_in_transfer_details = $db->getTransferNo($soNo, TRUE);
		print_r($transfer_details);
		if(! empty($transfer_details) )
		{
			$returnSO = new storeReturn();
			$returnSO->enterTransferReceipt();

			$transfer_no = $transfer_details[0]['transfer_no'];
			$slot_code   = $transfer_details[0]['slot_code'];
			$validate    = $returnSO->enterTransferNumber($transfer_no);

			if($validate) $returnSO->enterSOForm($transfer_no, $slot_code,$transfer_details, $not_in_transfer_details);
			$returnSO->logout($execParams);
		}
		else {
			echo " \n No receiver_nos found!. \n";
		}
	}

}

$db->close(); //close db connection