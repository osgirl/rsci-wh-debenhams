<?php

include_once('config/config.php');

class eWMSMigration {

	var $pdo;
	var $dbName;		//database name
	var $user;   			//Username for the database
   	var $pass;  				//Password
   	var $host; 		//hostname
   	var $fieldSeparator = ",";
   	var $fieldEnclosedBy = '"';
   	var $fieldEscapedBy = '';
	var $lineSeparator = "\n";
	// var $db2DumpDir = 'db2_dump/'; 	//directory of the csv files
	var $eWMSDumpDir = 'ewms_dump/'; 	//directory of the csv files
	var $filename;




	public function __construct(){
		$creds = mysql_credentials();
		$this->dbName = $creds['db_name'];
		$this->user = $creds['user'];
		$this->pass = $creds['password'];
		$this->host = $creds['hostname'];

		$this->connectDatabase();
	}

	/**
	 * Check if file exist
	 */
	private function isFileExist($csv_dir) {
		echo "\n $csv_dir \n";
		if(!file_exists($csv_dir)) {
			echo "File not found. Make sure you specified the correct path.";

			return false;
		}

		return true;
	}

	/**
	 * PDO Mysql Connection
	 */
	private function connectDatabase() {
		echo "cron/ewms_connection: mysql:host={$this->host};dbname={$this->dbName} \n";
		try {
		    $pdo = new PDO("mysql:host={$this->host};dbname={$this->dbName}; --local-infile",
		        $this->user, $this->pass,
		        array(
		            PDO::MYSQL_ATTR_LOCAL_INFILE => true,
		            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		        )
		    );

		    $this->pdo = $pdo;
		} catch (PDOException $e) {
		    die("database connection failed: ".$e->getMessage());
		}
	}

	private function sqlFilename($table) {
		$dateFormat = date('Ymd').'-'.time();
		$this->filename	= "{$this->eWMSDumpDir}{$table}_{$dateFormat}.sql";
	}

	/**
	 * Load csv file to the ewms database specified table
	 */
	public function import($csv_dir, $table, $columns, $use_replace = FALSE) {
		$result = $this->isFileExist($csv_dir);

		$ignore_replace = ($use_replace == FALSE) ? 'IGNORE' : 'REPLACE';

		if($result) {
			$query = "LOAD DATA LOCAL INFILE ".$this->pdo->quote($csv_dir)."
					{$ignore_replace}
			    	INTO TABLE `$table`
			    	FIELDS TERMINATED BY ".$this->pdo->quote($this->fieldSeparator). "
			    		   ENCLOSED BY ".$this->pdo->quote($this->fieldEnclosedBy). "
			    		   ESCAPED BY ".$this->pdo->quote($this->fieldEscapedBy). "
			    	LINES TERMINATED BY ".$this->pdo->quote($this->lineSeparator) . "
			    	IGNORE 1 LINES " .$columns;

			echo "$query \n";
			$affectedRows = $this->pdo->exec($query);
			// $affectedRows = $this->pdo->exec('mysql -uroot -proot ssi --local-infile');


			echo "Loaded a total of $affectedRows records from this csv file.\n";

			return $result;
		}
	}

	public function _resetPrimaryId($table) {
		echo "\n Resetting primary key \n";
		$query = "SET @num := 0;
					UPDATE {$table} SET id = @num := (@num+1);";
		echo "$query \n";
		$this->pdo->exec($query);
	}

	/**
	 * Update table PO_detail table to assign the po_od respectively
	 */
	public function _setPoIds($table, $result) {
		echo "\n Get the PO id \n";

		if($result) {
			$query = "SELECT id, receiver_no, created_at FROM {$table}
						WHERE created_at LIKE '%{$this->getDate()}%'
						GROUP BY receiver_no
						ORDER BY id";

			echo "\n $query \n";

			$table_detail = 'wms_purchase_order_details';
			foreach ($this->pdo->query($query) as $value ) {
				$this->_setDetailIds($table_detail, $value['id'], $value['receiver_no']);
		        /*print $row['id'] . "\t";
		        print $row['receiver_no'] . "\t";
		        print $row['created_at'] . "\n";*/
		    }
		}else {
			echo "\n Nothing to update!";
		}
	}

	private function _setDetailIds($table, $po_id, $receiver_no) {
		$sql = "UPDATE {$table} SET po_id = {$po_id} WHERE receiver_no = {$receiver_no}";

		echo "\n $sql \n";
		$this->pdo->exec($sql);
	}

	public function getDate() {
		return $this->dateToday = date('Y-m-d'); //format of date in db2: 140509
	}

	/**
	 * Match to filename against the $filename_pattern to get the latest csv file
	 */
	public function getLatestCsv($filename_pattern) {
		$files = glob('db2_dump/'.$filename_pattern.'_*.csv');
		// $files = array_combine($files, array_map('filectime', $files));
		rsort($files);
		$key   = $files[0]; // the filename
		echo "\n CSV FILE: $key \n";
		return $key;
	}

	/**
	 * Backup specified table from the ewms database
	 */
	public function mysqlDump($table) {
		$this->sqlFilename($table);
		$mysqldump = $this->_checkOSEnvironment();
		echo "Executing mysqldump on table: $table \n";
		// $dump_query = "c:/xampp/mysql/bin/mysqldump –u root -h localhost `ssi` `wms_purchase_order_lists` > test.sql";
		$dump_query = "{$mysqldump} -u{$this->user} -h {$this->host} {$this->dbName} {$table} > {$this->filename}";
		echo exec($dump_query);
	}

	public function _checkOSEnvironment() {
		$string = php_uname();
		$pattern = 'Windows';

		$pos = strpos($string, $pattern);

		// Note our use of ===.  Simply == would not work as expected
		// because the position of 'a' was the 0th (first) character.
		if ($pos === false) {
			//linux, etc
		    $command = 'mysqldump';
		} else {
		    //for windows testing
		    $command = 'c:/xampp/mysql/bin/mysqldump';
		}
		return $command;
	}

	public function close() {
		echo "Closing pdo connection... \n";
		$this->pdo = NULL;
	}

}