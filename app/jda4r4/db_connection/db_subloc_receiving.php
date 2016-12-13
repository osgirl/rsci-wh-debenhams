<?php
require_once(__DIR__.'/../config/config.php');
require_once(__DIR__ . '/../db_connection/db_connection.php');
require_once(__DIR__.'/../jda_connection/jda_connection.php');

class db_subloc_receiving_function {

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

    public function getClosedSublocReceive() {
        $query = "SELECT so_no  as move_doc_number from wms_store_return where so_status=23 and sync_status = '0'";
        return $this->sql_conn->runQuery($query);
    }

    public function getQtySublocReceive($mts_no) {
        $query = "  SELECT wms_product_lists.sku as sku, wms_store_return_detail.received_qty  as moved_qty
                    from wms_store_return_detail
                    left join wms_product_lists on wms_store_return_detail.sku = wms_product_lists.upc
                    where so_no ='$mts_no'";
        return $this->sql_conn->runQuery($query);
    }

    
  /*  public function updateIsSyncedSublocReceive($doc_no) {
        $query =  " UPDATE wms_store_return_detail set sync_status =1 where so_no = '$doc_no'";
        return $this->sql_conn->updateQuery($query);
    }*/
    
    

    public function JDAUpdateSublocReceiveQty($whmove, $inumbr, $qty) {
        $query = "UPDATE ".$this->jda->getLibrary().".trfdtl 
        SET trfrec = ".$qty." 
        WHERE trfbch  = ".$whmove." 
        AND inumbr ='".$inumbr."'"; 
        return $this->jda->runDb2Query($query);
    }
}