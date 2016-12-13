<?php
require_once(__DIR__.'/../config/config.php');
require_once(__DIR__ . '/../db_connection/db_connection.php');
require_once(__DIR__.'/../jda_connection/jda_connection.php');

class db_return_warehouse_function {

    private $jda;

    function __construct()
    {
        $config = mysql_credentials();
        $this->sql_conn = new Sqlconnect($config['hostname'], $config['database'], $config['username'], $config['password']);

        $this->jda = new JDAConnect();
        $this->jda->connect();

    }

    public function Connect() {
        return $this->sql_conn->Connect();
    }

    public function getClosedRW() {
        $query = "SELECT move_doc_number from wms_reverse_logistic where so_status=23 and sync_to_jda = '0'";
        return $this->sql_conn->runQuery($query);
    }

    public function getQtyRW($mts_no) {
        $query = "  SELECT wms_reverse_logistic_det.upc as sku, moved_qty 
                    from wms_reverse_logistic_det
                    left join wms_product_lists on wms_reverse_logistic_det.upc = wms_product_lists.upc
                    where move_doc_number ='$mts_no'";
        return $this->sql_conn->runQuery($query);
    }

    
    public function updateIsSyncedRW($doc_no) {
        $query =  " UPDATE wms_reverse_logistic set sync_to_jda =1 where move_doc_number = '$doc_no'";
        return $this->sql_conn->updateQuery($query);
    }
    
    

    public function JDAUpdateRWQty($whmove, $inumbr, $qty) {
        $query = "UPDATE ".$this->jda->getLibrary().".trfdtl 
        SET trfrec = ".$qty." 
        WHERE trfbch  = ".$whmove." 
        AND inumbr ='".$inumbr."'"; 
        return $this->jda->runDb2Query($query);
    }
}