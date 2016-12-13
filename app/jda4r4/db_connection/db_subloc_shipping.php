<?php
require_once(__DIR__.'/../config/config.php');
require_once(__DIR__ . '/../db_connection/db_connection.php');
require_once(__DIR__.'/../jda_connection/jda_connection.php');

class DB_function_subloc_shipping {

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
    public function getLoadCodeShipSubloc()
    {
    
    $query = "SELECT move_doc_number from wms_store_return_pickinglist where  pl_status = '18' and sync_to_jda = '1'";
        return $this->sql_conn->runQuery($query);
     }
  /*public function getShippingSubloc($loadCode) {

        $query = "SELECT wms_picklist.transfer_no  as move_doc_number FROM wms_picklist where move_doc_number=94";
       $query = "SELECT  wms_store_return_pickinglist.move_doc_number
                    FROM wms_load
                    left join wms_load_details on wms_load_details.load_code = wms_load.load_code
                    left join wms_box on wms_load_details.box_number = wms_box.box_code
                    left join wms_store_return_pickinglist on wms_box.tl_number = wms_store_return_pickinglist.move_doc_number
                    where wms_load.tagging_load=2 and wms_load.load_code='$loadCode' GROUP by wms_store_return_pickinglist.move_doc_number";
        return $this->sql_conn->runQuery($query);
    }*/
  /*  public function JDAUpdateWHShipping($whmove) {
       
        return $this->jda->runDb2Query($query);

    }*/
    public function updateIsSyncedShipping($mts_no) {
        $query =  " UPDATE wms_store_return_pickinglist set sync_to_jda = '2' where move_doc_number = '$mts_no' and pl_status= '18'";
        return $this->sql_conn->updateQuery($query);
    }

}