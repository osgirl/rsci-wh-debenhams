<?php

include_once(__DIR__.'/../core/jda5250_helper.php');
include_once(__DIR__.'/../sql/mysql.php');

class poClosing extends jdaCustomClass
{
	private static $formMsg = "";

	public static $user = 'SYS';
	public static $warehouseNo = "7000 ";


/*
	08
	02
	02
	etner receviner no: 37091
	tab
	ENTER
	tab
	incvoice numer: ADASD
	TAB
	TAB
	Invoice amout: 12
	TAB
	F5
	1
	Enter qty rec: 12
	END
	TAB
	F7
	TAB
	1
	F7
	ENTER
	F1
	F7
user: STRATPGMR pass: PASSWORD
*/
	public function __construct() {
		// parent::__construct();
		self::$formMsg = __METHOD__;
		parent::login(5);
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

	public function enterReceiverNumber($receiver_no, $back_order)
	{
		parent::$jda->screenWait("Receiver Number");
		parent::display(parent::$jda->screen,132);

		$formValues = array();//values to enter to form
		$formValues[] = array(sprintf("%10d", $receiver_no),8,45);
		$formValues[] = array(sprintf("%2d", $back_order),14,45);

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
            $receiver_message='Receiver is already being received by another user';
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

	public function enterPOForm($receiver_no)
	{
		parent::$jda->screenWait("Date Received");
		parent::display(parent::$jda->screen,132);
		self::enterPoStoreReceipt($receiver_no);
	}

	private static function enterPoStoreReceipt($receiver_no) {
		// $invoices = self::getInvoices($receiver_no);

		$formValues = array();//values to enter to form
		$formValues[] = array(self::$user,12,69);  //enter receive by
		$formValues[] = array(self::$user,17,72);  //enter checked by
		parent::$jda->write5250($formValues,F5,true);
		echo "Entered: Purchase Order Store Receipt  \n";
	}

	private static function checkReceiverLanding($reference) {
		parent::$jda->screenWait("Receiver Number");
		parent::display(parent::$jda->screen,132);
		//when it lands here we can now assume that the transaction was a success
		//need more test here
		self::updateSyncStatus($reference);
	}

	private static function getInvoices($receiver_no) {
		$db = new pdoConnection();

		echo "\n Getting invoice number and amount from db \n";
		$sql 	= "SELECT invoice_no, invoice_amount FROM wms_purchase_order_lists WHERE receiver_no = {$receiver_no}";
		$query 	= $db->query($sql);

		$result = array();

		foreach ($query as $value ) {
			$result['invoice_no'] = $value['invoice_no'];
			$result['invoice_amount'] = $value['invoice_amount'];
		}

		if(!empty($result))
		{
			if( empty($result['invoice_no']) )
			{
				self::$formMsg = "Invoice no is empty.";
				parent::logError(self::$formMsg, __METHOD__);
			}
			if( empty($result['invoice_amount']) )
			{
				self::$formMsg = "Invoice no is amount.";
				parent::logError(self::$formMsg, __METHOD__);
			}
		}

		$db->close();

		return $result;
	}

	private static function getQtyDelivered($receiver_no) {
		$db = new pdoConnection();

		echo "\n Getting quantity delivered from db \n";
		$sql = "SELECT prod.sku, po_details.quantity_delivered
				FROM `wms_transactions_to_jda` trans
				INNER JOIN wms_purchase_order_lists po_lists ON po_lists.purchase_order_no = trans.reference
				INNER JOIN wms_purchase_order_details po_details ON po_lists.receiver_no = po_details.receiver_no
				INNER JOIN wms_product_lists prod ON po_details.sku = prod.upc
				WHERE module = 'Purchase Order' AND jda_action='Closing' AND trans.sync_status = 0 AND po_details.quantity_ordered <> 0 AND po_lists.receiver_no = {$receiver_no}
				ORDER BY prod.sku ASC";

		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['quantity_delivered'];
		}

		$db->close();

		return $result;
	}

	public static function getNotInPoQtyDelivered($receiver_no) {
		$db = new pdoConnection();

		echo "\n Getting quantity delivered from db \n";
		$sql = "SELECT prod.sku, po_lists.slot_code, po_details.quantity_delivered
				FROM `wms_transactions_to_jda` trans
				INNER JOIN wms_purchase_order_lists po_lists ON po_lists.purchase_order_no = trans.reference
				INNER JOIN wms_purchase_order_details po_details ON po_lists.receiver_no = po_details.receiver_no
				LEFT JOIN wms_product_lists prod ON po_details.sku = prod.upc
				WHERE module = 'Purchase Order' AND jda_action='Closing' AND trans.sync_status = 0 AND po_details.quantity_ordered = 0 AND po_lists.receiver_no = {$receiver_no}
				ORDER BY prod.sku ASC";

		$query 	= $db->query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = array(
				'sku' => $value['sku'],
				'quantity_delivered' => $value['quantity_delivered'],
				'slot_code'	=> $value['slot_code']
			);
		}

		$db->close();

		return $result;
	}

	// enter not in po data
	private static function enterDataEntryMode($receiver_no) {
		$notInPo    = self::getNotInPoQtyDelivered($receiver_no);
		print_r($notInPo);
		$formValues = array();

		// if there is data insert item that is not in po
		if ( count($notInPo) > 0 )
		{
			foreach ($notInPo as $po)
			{
				$sku      = $po['sku'];
				$quantity = $po['quantity_delivered'];
				$slot     = $po['slot_code'];

				parent::$jda->write5250(null,F10,true);
				echo "Entered: Data Entry Mode  \n";
				parent::$jda->screenWait("Receiving Data Entry");
				parent::display(parent::$jda->screen,132);

				$formValues[] = array($sku,14,44);  //enter sku
				$formValues[] = array($quantity,15,44);  //enter quantity
				$formValues[] = array($slot,16,44);  //enter slot
				parent::$jda->write5250($formValues,ENTER,true);

				if(parent::$jda->screenCheck('Sku not on order. Press F9 to add.')) {
					parent::$jda->write5250(null,F9,true);
				}
			}
			//if done press F1
			parent::$jda->write5250(null,F1,true);
			self::checkResponse($receiver_no,__METHOD__);
		}
	}

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
					WHERE sync_status = 0 AND module = 'Purchase Order' AND jda_action='Closing' AND reference = (SELECT purchase_order_no FROM wms_purchase_order_lists po WHERE po.receiver_no = {$reference})";
		$query 	= $db->exec($sql);
		echo "Affected rows: $query \n";
		$db->close();
	}

	public function enterPoReceiptDetail() {
		parent::$jda->screenWait("Item Entry Selection Menu");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(array(array("1",16,41)),ENTER,true);
		echo "Entered: PO Receipt Detail  \n";
	}

	public function enterPoReceiptDetailBySKU() {
		parent::$jda->screenWait("Start at SKU");
		parent::display(parent::$jda->screen,132);
		echo "Entered: PO Receipt Detail by SKU  \n";
	}

	public function enterQtyPerItem($receiver_no)
	{
		$column 	= 10;
		$row 		= 100;
		$quantity 	= self::getQtyDelivered($receiver_no);
		$total      = count($quantity);
		$formValues = array();
		$limit      = 12;
		$offset     = 0;
		$count      = ceil($total / $limit);

		while($offset < $count) {
			$new = $offset;

			if($new !== 0) {
				$new = $new * $limit;
				echo "\nEntered ROLLUP: Page: {$offset} with offset of: {$new} and row {$row} \n";
				parent::$jda->write5250(null,ROLLUP,true);
			}

			$page = array_slice( $quantity, $new, $limit );
			foreach ($page as $key => $value) {
				$new_column = $key + $column;
				echo "\n value of new_col is: {$new_column} \n";
				echo "value of quantity is: {$value} \n";
				$formValues[] = array(sprintf("%10d", $value),$new_column,$row); //enter qty_delivered
			}
			parent::display(parent::$jda->screen,132);
			$offset++;
		}
		print_r($formValues);
		// for not in PO
		// self::enterDataEntryMode($receiver_no);
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250($formValues,F7,true);
		echo "Entered: Quantity per item/sku  \n";

		self::checkResponse($receiver_no,__METHOD__);
	}

	private static function checkResponse($receiver_no,$source)
	{
		# error
		if(parent::$jda->screenCheck('Qty received should not be greater than the qty ordered')) {
            $receiver_message="Qty received should not be greater than the qty ordered.";
			self::$formMsg = "{$receiver_no}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($receiver_no,"{$source}: {$receiver_message}", TRUE);
		}

		if(parent::$jda->screenCheck('Sku number cannot be zero.')) {
            $receiver_message="Sku number cannot be zero.";
			self::$formMsg = "{$receiver_no}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($receiver_no,"{$source}: {$receiver_message}", TRUE);
		}

		if(parent::$jda->screenCheck('Invalid sku entered.')) {
            $receiver_message="Invalid sku entered.";
			self::$formMsg = "{$receiver_no}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($receiver_no,"{$source}: {$receiver_message}", TRUE);
		}

		if(parent::$jda->screenCheck('Receipt quantity cannot be zero.')) {
            $receiver_message="Receipt quantity cannot be zero.";
			self::$formMsg = "{$receiver_no}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($receiver_no,"{$source}: {$receiver_message}", TRUE);
		}

		if(parent::$jda->screenCheck('Warehouse slot required.')) {
            $receiver_message="Warehouse slot required.";
			self::$formMsg = "{$receiver_no}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($receiver_no,"{$source}: {$receiver_message}", TRUE);
		}

		if(parent::$jda->screenCheck('Sku is not valid for the vendor.')) {
            $receiver_message="Sku is not valid for the vendor.";
			self::$formMsg = "{$receiver_no}: {$receiver_message}";
			parent::logError(self::$formMsg, __METHOD__);
			self::updateSyncStatus($receiver_no,"{$source}: {$receiver_message}", TRUE);
		}

		#end error

		echo self::$formMsg;
	}

	public function enterClosingPo()
	{
		parent::$jda->screenWait("Receiver Confirmation Print");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(null,TAB,true);
		parent::$jda->write5250(array(array("1",16,68)),F7,true);
		echo "Entered: Closing of PO  \n";
	}

	public function enterJobName($receiver_no)
	{
		parent::$jda->screenWait("Submitted Job Name");
		parent::display(parent::$jda->screen,132);
		parent::$jda->write5250(null,ENTER,true);
		echo "Entered: Job Name Queue.  \n";
		self::checkReceiverLanding($receiver_no);
	}

	/*
	* On done only via android
	*/
	public function enterUpToDockReceipt()
	{
		//TODO::checkvalues
		//TODO::how to know if error
		try {
			$title = "Closing PO \n";
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

	public function logout()
	{
		parent::logout();

		echo "Entered: Done purchase order closing.... \n";
	}
}

$db = new pdoConnection(); //open db connection

$params = array();
$params = array('module' => 'Purchase Order', 'jda_action' => 'Closing');

$execParams 			= array();
$execParams['poNo'] 	= ((isset($argv[1]))? $argv[1] : NULL);
if($argv[1]) $params['reference'] = $execParams['poNo'];

$poNos = $db->getJdaTransaction($params);

// $receiver_nos = $closePO->getReceiverNo();
print_r($poNos);
if(! empty($poNos) )
{
	$receiver_nos = $db->getReceiverNo($poNos);
	print_r($receiver_nos);
	if(! empty($receiver_nos) )
	{
		$closePO = new poClosing();
		$closePO->enterUpToDockReceipt();

		foreach($receiver_nos as $receiver_no)
		{
			$receiver   = $receiver_no['receiver_no'];
			$back_order = $receiver_no['back_order'];
			$validate   = $closePO->enterReceiverNumber($receiver, $back_order);

			if($validate)
			{
				$closePO->enterPOForm($receiver);
				$closePO->enterPoReceiptDetail();
				$closePO->enterPoReceiptDetailBySKU();
				//TODOS: need validation if qty is more than
				$closePO->enterQtyPerItem($receiver);
				$closePO->enterClosingPo();
				$closePO->enterJobName($receiver);
			}
		}
		$closePO->logout();
	}
	else {
		echo " \n No receiver_nos found!. \n";
	}
}
else {
	echo " \n No rows found!. \n";
}
$db->close(); //close db connection




/*$closePO = new poClosing();
$receiver_nos = $closePO->getReceiverNo();
print_r($receiver_nos);
if(! empty($receiver_nos) )
{
	$closePO->enterUpToDockReceipt();
	foreach($receiver_nos as $receiver) {
		$validate = $closePO->enterReceiverNumber($receiver);
		if($validate)
		{
			$closePO->enterPOForm($receiver);
			$closePO->enterPoReceiptDetail();
			$closePO->enterPoReceiptDetailBySKU();
			//TODOS: need validation if qty is more than
			$closePO->enterQtyPerItem($receiver);
			$closePO->enterClosingPo();
			$closePO->enterJobName($receiver);
		}
	}
}
else {
	echo " \n No rows found!. \n";
}


$closePO->logout();*/
