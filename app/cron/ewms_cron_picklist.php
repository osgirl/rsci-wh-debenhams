<?php
//use this
// */5 * * * * php /var/www/html/ssi-wms-portal/app/cron/ewms_cron_picklist.php
include('config/config.php');

$db = mysql_credentials();
$dsn = "mysql:dbname=" . $db["db_name"] . ";host=" . $db["hostname"];

$sql = <<<SQL
Select move_doc_number from wms_picklist where pl_status = 0;
SQL;

try {
    $pdo = new PDO($dsn, $db["user"], $db["password"]);
    $result = $pdo->query($sql);
    $result = $result->fetchAll();

   	foreach ($result as $key => $value) {
   		$docNo = $value["move_doc_number"];
      
$sql = <<<SQL
Select count(*) as count from wms_picklist_details where move_doc_number = $docNo;
SQL;
		$originalCount = $pdo->query($sql);
		$originalCount = $originalCount->fetchAll();
    $originalCount = $originalCount[0]['count'];

$sqlMoved = <<<SQL
Select count(*) as count from wms_picklist_details where move_doc_number = $docNo and move_to_shipping_area = 1;
SQL;
    $movedCount = $pdo->query($sqlMoved);
    $movedCount = $movedCount->fetchAll();
    $movedCount = $movedCount[0]['count'];
      if($originalCount == $movedCount) {
$sqlUpdateHeader = <<<SQL
Update wms_picklist set pl_status=1 where move_doc_number = $docNo;
SQL;
$updateHeaderResult = $pdo->query($sqlUpdateHeader);

      } //end if

   	}//end foreach
   
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
    echo "\n";
}
?>
