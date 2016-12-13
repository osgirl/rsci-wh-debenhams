<?php
require_once(__DIR__.'/../config/config.php');
require_once(__DIR__ . '/../db_connection/db_connection.php');
require_once(__DIR__.'/../jda_connection/jda_connection.php');

class DB_Picking_Functions {

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

    public function getClosedPicking() {
        $query = "SELECT move_doc_number from wms_picklist where pl_status= '18' and sync_to_jda = '0'";
         ///sync to jda = 0;  the transfer number was closed from portal 
        return $this->sql_conn->runQuery($query);
    }

    public function getQtyPicked($mts_no) 
    {
        $query = "  SELECT wms_product_lists.sku, moved_qty 
                    from wms_picklist_details
                    left join wms_product_lists on wms_picklist_details.sku = wms_product_lists.upc
                    where move_doc_number ='$mts_no'";
        return $this->sql_conn->runQuery($query);
    }

    
    
    public function updateIsSynced($doc_no) {
        $query =  " UPDATE wms_picklist set sync_to_jda =1 where move_doc_number = '$doc_no'";
        return $this->sql_conn->updateQuery($query);
    }
    
    
    

    public function JDAUpdatePickedQty($whmove, $inumbr, $qty) {
        $query = "UPDATE ".$this->jda->getLibrary().". whsmvd 
        SET whmvqm = ".$qty." 
        WHERE whmove  = ".$whmove." 
        AND inumbr ='".$inumbr."'"; 
        return $this->jda->runDb2Query($query);
    }

}