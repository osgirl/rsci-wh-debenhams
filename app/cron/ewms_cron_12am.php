<?php

$sql = <<<SQL
Update wms_sku_on_dock set total_qty_delivered= 0 where total_qty_remaining = 0;
SQL;


$db = array(
    "hostname" => "localhost",
    "user"     => "root",
    "password" => 'pass',
    "db_name"  => "ssi"
);
$dsn = "mysql:dbname=" . $db["db_name"] . ";host=" . $db["hostname"];

try {
    $pdo = new PDO($dsn, $db["user"], $db["password"]);
    $result = $pdo->exec($sql);
    var_dump($result);
    
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
    echo "\n";
}

?>
