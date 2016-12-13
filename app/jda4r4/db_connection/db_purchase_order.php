<?php
require_once(__DIR__.'/../config/config.php');
require_once(__DIR__ . '/../db_connection/db_connection.php');
require_once(__DIR__.'/../jda_connection/jda_connection.php');

class db_purchase_order_function {

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

    public function getClosedPO() {
        $query = "SELECT receiver_no, invoice_no, po_status, purchase_order_no from wms_purchase_order_lists where (po_status = 5 or po_status = 6) and sync_to_jda = '0'";
        return $this->sql_conn->runQuery($query);
    }

    public function getQtyPO($mts_no) {
        $query = "  SELECT wms_purchase_order_details.sku, quantity_delivered as moved_qty, slot_code
                    from wms_purchase_order_details 
                    where receiver_no ='$mts_no'";
        return $this->sql_conn->runQuery($query);
    }

    
    public function updateIsSyncedPO($doc_no) {
        $query =  " UPDATE wms_purchase_order_lists set sync_to_jda =1, updated_at=date('Y-m-d H:i:s') where receiver_no = '$doc_no'";
        return $this->sql_conn->updateQuery($query);
    }
    public function getNotInPO($po_no, $receiver_no)
    {
    $query = "SELECT wms_product_lists.sku,  quantity_delivered, wms_purchase_order_detailS.receiver_no 
from wms_purchase_order_lists 
left join wms_purchase_order_details on wms_purchase_order_lists.receiver_no = wms_purchase_order_details.receiver_no 
left join wms_product_lists on wms_purchase_order_details.sku = wms_product_lists.upc 
    where wms_purchase_order_lists.purchase_order_no = '$po_no' and wms_purchase_order_details.receiver_no = '$receiver_no' and quantity_ordered = '0' ";
        return $this->sql_conn->runQuery($query);
    }
    
    

    public function JDAUpdatePOQty($whmove, $inumbr, $qty) {
        $query = "UPDATE ".$this->jda->getLibrary().".POMRCD 
        SET pomcur = ".$qty." 
        WHERE pomrcv  = ".$whmove." 
        AND inumbr ='".$inumbr."'"; 
        return $this->jda->runDb2Query($query);
    }
}