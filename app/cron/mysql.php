<?php

$databasehost = "localhost"; 
$databasename = "test_ssi"; 
$databasetable = "wms_purchase_order_lists"; 
$databaseusername="root"; 
$databasepassword = ""; 
$fieldseparator = ","; 
$lineseparator = "\n";
// $csvfile = "dump_data/purchase_order_20140509-1399602510.csv";
$csvfile = "dump_data/purchase_order_20140509-1399602510-test.csv";

$columns = '(vendor_id, receiver_no, purchase_order_no, destination, po_status)';

if(!file_exists($csvfile)) {
    die("File not found. Make sure you specified the correct path.");
}

try {
    $pdo = new PDO("mysql:host=$databasehost;dbname=$databasename", 
        $databaseusername, $databasepassword,
        array(
            PDO::MYSQL_ATTR_LOCAL_INFILE => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        )
    );
} catch (PDOException $e) {
    die("database connection failed: ".$e->getMessage());
}

//POVNUM, POMRCV, PONUMB, POLOC, POSTAT
//vendor_id | receiver_no | purchase_order_no | destination | po_status |
//30105			20110			10151				9005			3


//NOTE: For IGNORE to work a table has to have a unique index
// CREATE UNIQUE INDEX index_name ON table_name (column_name)
$query = "LOAD DATA LOCAL INFILE ".$pdo->quote($csvfile)." 
	  IGNORE 
      INTO TABLE `$databasetable`
      FIELDS TERMINATED BY ".$pdo->quote($fieldseparator)."
      LINES TERMINATED BY ".$pdo->quote($lineseparator) . " " .$columns;

echo "$query \n";
$affectedRows = $pdo->exec($query);

echo "Loaded a total of $affectedRows records from this csv file.\n";

function backupTable() {
	echo "Exeucted \n";
	// $dump_query = "c:/xampp/mysql/bin/mysqldump –u root -h localhost `ssi` `wms_purchase_order_lists` > test.sql";
	$dump_query = "c:/xampp/mysql/bin/mysqldump -uroot -h localhost ssi wms_purchase_order_lists > test.sql";
	echo exec($dump_query);
}

backupTable();