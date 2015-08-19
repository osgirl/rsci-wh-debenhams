<?php
include_once('../../config/config.php');

class pdoConnection
{
	public static $pdo;
	public static $dbName;// 	= 'ssi';		//database name
	public static $user;// 	= 'root';  			//Username for the database
   	public static $pass;// 	= 'root'; 				//Password
   	public static $host;// 	= 'localhost';		//hostname

	public function __construct(){
		$creds = mysql_credentials();
		self::$dbName = $creds['db_name'];
		self::$user = $creds['user'];
		self::$pass = $creds['password'];
		self::$host = $creds['hostname'];

		self::connectDatabase();
	}

	/**
	 * PDO Mysql Connection
	 */
	public static function connectDatabase() {
		echo "sql/mysql: mysql:host=".self::$host.";dbname=".self::$dbName."\n";
		try {
		    $pdo = new PDO("mysql:host=".self::$host.";dbname=".self::$dbName."; --local-infile",
		        self::$user, self::$pass,
		        array(
		            PDO::MYSQL_ATTR_LOCAL_INFILE => true,
		            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		        )
		    );

		    self::$pdo = $pdo;
		} catch (PDOException $e) {
		    die("database connection failed: ".$e->getMessage());
		}
	}

	public function query($sql) {
		echo "\n $sql \n";

		return self::$pdo->query($sql);
	}

	public function exec($sql)
	{
		echo "\n $sql \n";

		return self::$pdo->exec($sql);
	}

	public function getJdaTransaction($data = array())
	{
		$module = $data['module'];
		$jdaAction = $data['jda_action'];

		echo "\n Getting reference # from db \n";

		$sql 	= "SELECT reference FROM wms_transactions_to_jda
					WHERE module = '{$module}' AND jda_action='{$jdaAction}'";

		if(!empty($data['reference'])) $sql .= " AND reference = '{$data['reference']}'";

		if(!empty($data['checkSuccess']))
			$sql .=" AND sync_status <> 1";
		else
			$sql .=" AND sync_status = 0";
		/*if(!empty($data['reference']))
		{
			// print_r($data['reference']);
			$reference = json_decode($data['reference']);
			$decodedReference = "'" . implode("','", $reference) . "'";
			$sql .= " AND reference IN ({$decodedReference})";
		}*/

		$query = self::query($sql);
		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['reference'];
		}

		return $result;
	}

	/**
	* Get picklist doc nos
	*
	* @param $data 	array()		array values are module, jda_action & reference
	* @return array of reference
	*/
	public function getJdaTransactionPicklist($data = array())
	{
		$module 	= $data['module'];
		$jdaAction 	= $data['jda_action'];

		echo "\n Getting reference # from db \n";

		if(!empty($data['reference']))
		{
			/*$sql = "SELECT DISTINCT reference FROM wms_transactions_to_jda trans
					INNER JOIN wms_picklist_details pick_d ON pick_d.move_doc_number = trans.reference
					INNER JOIN wms_box_details bd ON bd.picklist_detail_id = pick_d.id
					INNER JOIN wms_pallet_details pd ON bd.box_code = pd.box_code
					INNER JOIN wms_load_details ld ON ld.pallet_code = pd.pallet_code AND ld.load_code = '{$data['reference']}'
					WHERE module = '{$module}' AND jda_action = '{$jdaAction}' AND trans.sync_status = 0";*/
			$sql = "SELECT DISTINCT reference FROM wms_transactions_to_jda trans
					INNER JOIN wms_picklist_details pick_d ON pick_d.move_doc_number = trans.reference
					INNER JOIN wms_box_details bd ON bd.picklist_detail_id = pick_d.id
					WHERE module = '{$module}' AND jda_action = '{$jdaAction}' AND reference = {$data['reference']} AND trans.sync_status = 0";
					// INNER JOIN wms_pallet_details pd ON bd.box_code = pd.box_code
					// INNER JOIN wms_load_details ld ON ld.pallet_code = pd.pallet_code
		}
		else {
			$sql = "SELECT DISTINCT reference FROM wms_transactions_to_jda trans
					INNER JOIN wms_picklist_details pick_d ON pick_d.move_doc_number = trans.reference
					INNER JOIN wms_box_details bd ON bd.picklist_detail_id = pick_d.id
					WHERE module = '{$module}' AND jda_action = '{$jdaAction}' AND trans.sync_status = 0";
					// INNER JOIN wms_pallet_details pd ON bd.box_code = pd.box_code
					// INNER JOIN wms_load_details ld ON ld.pallet_code = pd.pallet_code
		}

		$query = self::query($sql);
		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['reference'];
		}

		return $result;
	}


	/**
	* Get picklist of load
	*
	* @param $data 	array()		array values are module, jda_action & reference
	* @return array of reference
	*/
	public function getPicklistsOfLoad($loadCode)
	{
		$sql = "SELECT group_concat(DISTINCT move_doc_number SEPARATOR ',') move_doc_number FROM wms_load_details load_d
				RIGHT JOIN wms_pallet_details pallet_d ON pallet_d.pallet_code = load_d.pallet_code
				LEFT JOIN wms_box_details box_d ON box_d.box_code=pallet_d.box_code
				LEFT JOIN wms_picklist_details picklist_d ON picklist_d.id=box_d.picklist_detail_id
				WHERE load_code='{$loadCode}'";

		$query = self::query($sql);
		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['move_doc_number'];
		}

		return $result[0];
	}

	/**
	* Get boxes
	*
	* @param $data 	array()		array values are module, jda_action & reference
	* @return array of reference
	*/
	public function getJdaTransactionBoxHeader($data = array())
	{
		$module 	= $data['module'];
		$jdaAction 	= $data['jda_action'];

		echo "\n Getting reference # from db \n";

		if(!empty($data['reference']))
		{
			$sql = "SELECT DISTINCT reference FROM wms_transactions_to_jda trans
					INNER JOIN wms_pallet_details pd ON trans.reference = pd.box_code
					INNER JOIN wms_load_details ld ON ld.pallet_code = pd.pallet_code AND ld.load_code = '{$data['reference']}'
					WHERE module = '{$module}' AND jda_action = '{$jdaAction}'";
		}
		else {
			$sql 	= "SELECT DISTINCT reference FROM wms_transactions_to_jda trans
					INNER JOIN wms_pallet_details pd ON trans.reference = pd.box_code
					INNER JOIN wms_load_details ld ON ld.pallet_code = pd.pallet_code
					WHERE module = '{$module}' AND jda_action = '{$jdaAction}'";
		}

		if(!empty($data['checkSuccess']))
			$sql .=" AND trans.sync_status <> 1";
		else
			$sql .=" AND trans.sync_status = 0";

		$query = self::query($sql);
		$result = array();
		foreach ($query as $value ) {
			 $result[] = self::getPicklistStatusInLoad($value['reference']);
		}
		return $result;
	}


	public function getPicklistStatusInLoad($box_code){
		    $sql 	= "SELECT wms_box_details.box_code FROM `wms_box_details` LEFT JOIN `wms_pallet_details` ON
		    `wms_box_details`.`box_code` = `wms_pallet_details`.`box_code` LEFT join `wms_picklist_details` ON
		    `wms_picklist_details`.`id` = `wms_box_details`.`picklist_detail_id` LEFT JOIN `wms_picklist` ON
		    `wms_picklist`.`move_doc_number` = `wms_picklist_details`.`move_doc_number`
		    WHERE wms_picklist.pl_status=18 AND wms_box_details.box_code='{$box_code}'";
			 $query = self::query($sql);
			 foreach ($query as $value ) {
			 	$result = $value['box_code'];
			 }
		 return $result;
	}

	/**
	* Get pallets
	*
	* @param $data 	array()		array values are module, jda_action & reference
	* @return array of reference
	*/
	public function getJdaTransactionPallet($data = array())
	{
		$module 	= $data['module'];
		$jdaAction 	= $data['jda_action'];

		echo "\n Getting reference # from db \n";

		if(!empty($data['reference']))
		{
			$sql = "SELECT DISTINCT reference FROM wms_transactions_to_jda trans
					INNER JOIN wms_pallet_details pd ON trans.reference = pd.pallet_code
					INNER JOIN wms_load_details ld ON ld.pallet_code = pd.pallet_code AND ld.load_code = '{$data['reference']}'
					WHERE module = '{$module}' AND jda_action = '{$jdaAction}'";
		}
		else {
			$sql = "SELECT DISTINCT reference FROM wms_transactions_to_jda trans
					INNER JOIN wms_pallet_details pd ON trans.reference = pd.pallet_code
					INNER JOIN wms_load_details ld ON ld.pallet_code = pd.pallet_code
					WHERE module = '{$module}' AND jda_action = '{$jdaAction}'";
		}

		if(!empty($data['checkSuccess']))
			$sql .=" AND trans.sync_status <> 1";
		else
			$sql .=" AND trans.sync_status = 0";

		$query = self::query($sql);
		$result = array();
		foreach ($query as $value ) {
			$result[] = $value['reference'];
		}

		return $result;
	}

	public function getReceiverNo($poNo) {
		$poNo = join(',', $poNo);
		echo "\n Getting receiver no from db \n";
		$sql 	= "SELECT receiver_no, back_order, slot_code, shipment_reference_no FROM wms_purchase_order_lists
					WHERE purchase_order_no IN ({$poNo})
					ORDER BY purchase_order_no ASC";
		$query 	= self::query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] =  array(
				'receiver_no' => $value['receiver_no'],
				'back_order' => $value['back_order'],
				'shipment_reference_no' => (empty($value['shipment_reference_no'])) ? '0' : $value['shipment_reference_no'],
				'slot_code' => $value['slot_code']);
		}

		return $result;
	}

	public function getTransferNo($soNo, $getNotInTransfer = FALSE) {

		echo "\n Getting transfer no from db \n";
		$sql 	= "SELECT wms_store_return.so_no,slot_code,wms_store_return_detail.delivered_qty,wms_store_return_detail.received_qty,wms_product_lists.sku FROM wms_store_return
					INNER JOIN wms_store_return_detail ON wms_store_return.so_no = wms_store_return_detail.so_no
					INNER JOIN wms_product_lists ON wms_store_return_detail.sku = wms_product_lists.upc
					WHERE wms_store_return.so_no = {$soNo}";

		if ($getNotInTransfer) $sql .= " AND delivered_qty = 0";
		// else $sql .= " AND delivered_qty <> 0";

		$sql .= " ORDER BY convert(wms_product_lists.sku, decimal) ASC";
		$query 	= self::query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] =  array(
				'transfer_no' => $value['so_no'],
				'delivered_qty' => $value['delivered_qty'],
				'received_qty' => $value['received_qty'],
				'slot_code' => $value['slot_code'],
				'sku' => $value['sku']);
		}

		return $result;
	}

	public function getMoveDetails($mmId) {
		echo "\n Getting manual move details from db \n";
		$sql 	= "SELECT pl.sku, wms_manual_move.* FROM wms_manual_move
					INNER JOIN wms_product_lists pl ON pl.upc = wms_manual_move.upc
					WHERE sync_status=0";
		if($mmId!=null)
			$sql .= " and wms_manual_move.id = {$mmId}";

		$sql .= " ORDER BY convert(pl.sku, decimal) ASC";
		$query 	= self::query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] =  array(
				'id' => $value['id'],
				'sku' => $value['sku'],
				'quantity' => $value['quantity'],
				'from_slot' => $value['from_slot'],
				'to_slot' => $value['to_slot']);
		}

		return $result;
	}

	public function getPutawaySkus()
	{
		echo "\n Getting data from slot_details \n";
		$sql 	= "SELECT slot_id, wms_product_lists.sku AS sku, quantity FROM wms_slot_details
					INNER JOIN wms_product_lists ON wms_slot_details.sku = wms_product_lists.upc
					WHERE sync_status = 0";
		$query 	= self::query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value;
		}

		return $result;
	}

	public function getPicklistInfo($docNo)
	{
		$docNo = join(',', $docNo);
		echo "\n Getting move doc number from db \n";
		$sql = "SELECT DISTINCT pl.move_doc_number, pd.store_code, MIN(bd.box_code) box_code
					FROM wms_box_details bd
					INNER JOIN wms_picklist_details pd ON bd.picklist_detail_id = pd.id
                    INNER JOIN wms_picklist pl ON pl.move_doc_number = pd.move_doc_number
					WHERE pl_status = 18 AND pl.move_doc_number IN ({$docNo})
					GROUP BY pl.move_doc_number
					ORDER BY pl.move_doc_number, sequence_no ASC";
					// GROUP BY picklist_detail_id
		$query 	= self::query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value;
		}

		return $result;
	}

	public function getPalletsInfo($palletCode)
	{
		// $palletCodex = (string) join(',', $palletCode);
		$pallet = "'" . implode("','", $palletCode) . "'";

		// print_r($pallet);die();
		echo "\n Getting box codes from db \n";
		$sql = "SELECT p.pallet_code, p.store_code FROM wms_pallet p
				WHERE p.pallet_code IN ({$pallet})
				GROUP BY p.pallet_code, p.store_code
				ORDER BY p.pallet_code ASC";
		$query 	= self::query($sql);

		$result = array();
		foreach ($query as $value ) {
			$result[] = $value;
		}

		return $result;
	}

	/*
	* Get boxes
	*/
	public function getSOBoxes()
	{
		$soStatus = 3;
		echo "\n Getting load_code from db \n";
		$sql = "SELECT DISTINCT b.box_code, b.store_code
					FROM wms_store_order so
					INNER JOIN wms_load_details ld ON ld.load_code = so.load_code
					INNER JOIN wms_pallet_details pd ON ld.pallet_code = pd.pallet_code
                    INNER JOIN wms_box b ON b.box_code = pd.box_code AND so.store_code = b.store_code
					WHERE so_status = $soStatus AND so.sync_status = 0
					GROUP BY b.box_code, so.load_code";
		$query 	= self::query($sql);

		$result = array();
		foreach ($query as $value ) {
			// $result[] = $value['box_code'];
			$result[] = $value;
		}

		return $result;
	}

	/**
    * Execute command in the background without PHP waiting for it to finish for Unix
    *
    * @example  instance->execInBackground();
    *
    * @param  $cmd       string command to execute
    * @return
    */
    private static function execInBackground($cmd,$source)
    {
        $cmd = 'nohup php -q ' . __DIR__.'/../../jda/' . $cmd;
    	$pidfile = __DIR__.'/../../jda/logs/pidfile.log';

    	$filename=$source . "_" . date('m_d_y');
    	$outputfile = __DIR__.'/../../jda/logs/'.$filename.'.log';
        exec(sprintf("%s >> %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));
        // exec($cmd . " </dev/null 2> /dev/null & echo $!");
        // exec($cmd . " > /dev/null &");
    }

    // TODO: validation
    public function daemon($filename, $data = NULL)
	{
		self::execInBackground("classes/{$filename}.php {$data}",$filename);
	}

	public function close() {
		echo "Closing pdo connection... \n";
		self::$pdo = NULL;
	}

}

/*$pdo = new pdoConnection();
$pdo->close();*/
