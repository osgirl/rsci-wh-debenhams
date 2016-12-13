<?php
require_once(__DIR__.'/../config/config.php');
require_once(__DIR__ . '/../db_connection/db_connection.php');
require_once(__DIR__.'/../jda_connection/jda_connection.php');

class DB_sublock_pick_function {

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

     public function getClosedSublocPicking() {
        $query = "SELECT move_doc_number from wms_store_return_pickinglist where pl_status=18 and sync_to_jda = '0'";
        return $this->sql_conn->runQuery($query);
    }
    public function getQtySublocPicked($mts_no) {
        $query = "  SELECT wms_product_lists.sku, moved_qty 
                    from   wms_store_return_pick_details
                    left join wms_product_lists on wms_store_return_pick_details.sku = wms_product_lists.upc
                    where move_doc_number ='$mts_no'";
        return $this->sql_conn->runQuery($query);
    }
     public function    JDAUpdateSublocPickedQty($whmove, $inumbr, $qty) {
        $query = "UPDATE ".$this->jda->getLibrary().". trfdtl 
        SET trfalc = ".$qty." 
        WHERE trfbch  = ".$whmove." 
        AND inumbr ='".$inumbr."'"; 
        return $this->jda->runDb2Query($query);

    }
     public function updateIsSyncedSubloc($doc_no) {
        $query =  " UPDATE wms_store_return_pickinglist set sync_to_jda ='1' where move_doc_number = '$doc_no'";
        return $this->sql_conn->updateQuery($query);
    }
}