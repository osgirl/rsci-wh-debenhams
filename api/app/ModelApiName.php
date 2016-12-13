<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Response;
use Request;
class ModelApiName extends Model

{


    //
    public static function name()
    {
    	//$query=DB::table('name')->select(DB::raw("*"))->get();
        $query = DB::select(DB::raw("SELECT * from name"));

    	$table  = 'name';
        $output = array();
            foreach ($query as $key => $row) {
                $output[$table][$key] = $row;
            }
        return $query;
    }

    public static function GetApiRPoList($piler_id) 
    {

            $query = DB::select(DB::raw("SELECT purchase_order_no,wms_purchase_order_lists.receiver_no,wms_purchase_order_details.dept_number as division_id,division,wms_purchase_order_details.po_status from wms_purchase_order_lists 
                inner JOIN wms_purchase_order_details on wms_purchase_order_lists.receiver_no=wms_purchase_order_details.receiver_no where wms_purchase_order_details.assigned_to_user_id=$piler_id and wms_purchase_order_details.po_status=3 group by wms_purchase_order_lists.receiver_no,dept_number"));
            
        return $query;
    }
    public static function GetApiSlotCodes() 
    {

            $query = DB::select(DB::raw("SELECT slot_code from wms_slot_lists"));       
        return $query;
    }

    public static function getAPIquery($query) {

        $query = DB::select(DB::raw($query));
            
        return $query;
    }
    public static function GetApiRPoListDetail($receiver_no,$division_id) {

        $query = DB::select(DB::raw("SELECT receiver_no,dept_number as division,wms_purchase_order_details.sku,wms_purchase_order_details.upc,description as description,quantity_ordered,quantity_delivered,po_status FROM wms_purchase_order_details inner join wms_product_lists on wms_purchase_order_details.sku=wms_product_lists.sku WHERE receiver_no=$receiver_no and dept_number='$division_id'"));
            
        return $query;
    }

    public static function UpdateApiRPoSlot($receiver_no,$division) {
        $query = DB::select(DB::raw("update wms_purchase_order_details set po_status=3 WHERE receiver_no=$receiver_no and division=$division"));
        return $query;
    }
    public static function  getAPIUserLoginVerify($username,$password) {
        $query = DB::select(DB::raw("select * from wms_users where username='$username' and deleted_at='0000-00-00 00:00:00' and (role_id='2' or role_id='8')"));
        return $query;
    }
    public static function   UserLogin($username,$password) {
        $query = DB::select(DB::raw("select * from wms_users where username='$username' and deleted_at='0000-00-00 00:00:00' and role_id=3"));
        return $query;
    }

    public static function UpdateRPoListDetail($receiver_no,$division_id,$upc,$rqty, $slot) {
        $query = DB::select(DB::raw("update wms_purchase_order_details set quantity_delivered='$rqty',po_status=4, slot_code='$slot' where receiver_no='$receiver_no' and dept_number='$division_id' and upc='$upc'"));
        return $query;
    }
    public static function RPoListDetailAdd($receiver_no,$division_id,$sku,$upc,$rqty,$userid,$slot,$division_name) {
        $query = DB::select(DB::raw("insert INTO wms_purchase_order_details VALUES('','$upc','$sku','$receiver_no','$slot','$division_id','0','$division_name','0','0','$rqty','0000-00-00 00:00:00','0','$userid','4',date('Y-m-d H:i:s'),date('Y-m-d H:i:s'),'0000-00-00 00:00:00')"));
        return $query;
    }

public static function UpdateApiRpoList($receiver_no,$division) {
          $query = DB::select(DB::raw("SELECT * from wms_purchase_order_details WHERE receiver_no=$receiver_no and po_status<>4"));
       if(count($query)==0)
        {
           $query =  DB::select(DB::raw("UPDATE wms_purchase_order_lists set po_status= '4' where receiver_no=$receiver_no"));
        }
    }
    public static function GetApiPTLList($piler_id) 
    {

        $query = DB::select(DB::raw("SELECT wms_picklist.move_doc_number,wms_stores.store_code,wms_stores.store_name from wms_picklist 
            inner join wms_picklist_details on wms_picklist.move_doc_number=wms_picklist_details.move_doc_number 
            inner join wms_stores on wms_picklist_details.store_code=wms_stores.store_code 
            where wms_picklist.assigned_to_user_id='$piler_id' and pl_status='16' group by wms_picklist_details.move_doc_number"));
      
        return $query;
    }

   /** public static function UpdateTLStatus($query) 
    {
        foreach ($query as $value) {
            DB::select(DB::raw("UPDATE wms_picklist set pl_status='17' where moved_doc_number='$value->move_doc_number")); 
    } 

    }**/
      

 public static function GetApiPTLListDetail($moved_doc) 
    {

        $query = DB::select(DB::raw("SELECT wms_picklist.move_doc_number,wms_picklist_details.id, wms_picklist_details.sku as upc,
            COALESCE(wms_product_lists.sku, 'Not Available') as sku, 
            COALESCE(wms_department.description,'Not Available') as dept, 
            COALESCE(wms_product_lists.short_description, 'Not Available') as style, 
            COALESCE(wms_product_lists.description,'Not Available') as short_description, quantity_to_pick, moved_qty from  wms_picklist 
            INNER JOIN wms_picklist_details ON wms_picklist.move_doc_number = wms_picklist_details.move_doc_number 
            LEFT JOIN wms_product_lists ON wms_picklist_details.sku = wms_product_lists.upc 
            LEFT JOIN wms_department ON wms_product_lists.dept_code=wms_department.dept_code AND wms_product_lists.sub_dept =wms_department.sub_dept AND wms_product_lists.class = wms_department.class AND wms_product_lists.sub_class=wms_department.sub_class WHERE wms_picklist.move_doc_number = '$moved_doc'"));
            
        return $query;

    }
    public static function ApiPTLListDetailUpdate($picking_id,$upc,$rcv_qty)
    {
        $query = DB::select(DB::raw("UPDATE wms_picklist_details set moved_qty='$rcv_qty'
        where id = '$picking_id' and  sku='$upc'"));   
        return $query;
    }
    public static function ApiPSTTLListDetailUpdate($picking_id,$upc,$rcv_qty)
    {
        $query = DB::select(DB::raw("UPDATE   wms_store_return_pick_details set moved_qty='$rcv_qty'
        where id = '$picking_id' and  sku='$upc'"));   
        return $query;
    }

    public static function ApiPTLListUpdate($moved_doc)
    {
        $query = DB::select(DB::raw(" UPDATE wms_picklist 
        SET pl_status=17
        WHERE move_doc_number='$moved_doc'"));   
        return $query;
    }
    public static function ApiPSTTLListUpdate($moved_doc)
    {
        $query = DB::select(DB::raw(" UPDATE wms_store_return_pickinglist 
        SET pl_status=17
        WHERE move_doc_number='$moved_doc'"));   
        return $query;
    }
     public static function GetApiPTLGetBoxCode($store_id) 
    {
        $query = DB::select(DB::raw("SELECT box_code,store_code as store_id,GROUP_CONCAT(move_doc_number) as move_doc,box_number as number,max(box_total)as total FROM wms_box WHERE store_code='$store_id' and assign_to_load=0  group by box_code"));   
        return $query;
    }
     public static function GetApiPSTTLGetBoxCode($store_id) 
    {
        $query = DB::select(DB::raw("SELECT box_code,store_code as store_id,GROUP_CONCAT(tl_number) as move_doc, box_number as number,max(box_total)as total FROM wms_box WHERE store_code='$store_id' and in_use=0 group by box_code"));   
        return $query;
    }

    public static function GetApiPTLGetLastBoxCode($store_id, $move_doc) 
    {
        $query = DB::select(DB::raw("SELECT MAX(box_code) as box_code,max(box_total) as total FROM wms_box WHERE store_code='$store_id' and  move_doc_number='$move_doc'"));   
        return $query;
    }
    public static function GetApiPSTTLGetLastBoxCode($store_id, $move_doc)
    {
        $query = DB::select(DB::raw("SELECT MAX(box_code) as box_code,max(box_total) as total FROM wms_box WHERE store_code='$store_id' and  tl_number='$move_doc'"));   
        return $query;
    }
    public static function GetApiPTLGetLastBoxCode1($store_id ) 
    {
        $query = DB::select(DB::raw("SELECT MAX(box_code) as box_code,max(box_total) as total FROM wms_box WHERE store_code='$store_id'"));   
        return $query;
    }

    public static function ApiPTLNewBoxCode($box_code,$store_id,$move_doc,$number,$total)
    {
        $query = DB::select(DB::raw("INSERT INTO 
            wms_box(`box_code`, `move_doc_number`, `store_code`, `box_number`, `box_total`) 
            VALUES ('$box_code','$move_doc','$store_id','$number','$total')"));   
        return $query;
    } 
    public static function ApiPSTTLNewBoxCode($box_code,$store_id,$move_doc,$number,$total)
    {
        $query = DB::select(DB::raw("INSERT INTO 
            wms_box(`box_code`, `tl_number`, `store_code`, `box_number`, `box_total`) 
            VALUES ('$box_code','$move_doc','$store_id','$number','$total')"));   
        return $query;
    } 
    public static function ApiPTLBoxDetailInsert($picklist_detail_id,$box_code,$moved_qty)
    {
        $query1 = DB::select(DB::raw("SELECT * from wms_box_details where picklist_detail_id = '$picklist_detail_id' and box_code='$box_code' "));
         if(count($query1)==0)
        {
            $query = DB::select(DB::raw("INSERT INTO wms_box_details (`picklist_detail_id`, `box_code`, `moved_qty`) VALUES('$picklist_detail_id','$box_code','$moved_qty')"));
        }
        else  {
             $query = DB::select(DB::raw("UPDATE wms_box_details  set  moved_qty='$moved_qty' WHERE picklist_detail_id='$picklist_detail_id' and box_code='$box_code'"));
        }

        return $query;

    } 
    public static function ApiSSTPTLBoxDetailInsert($picklist_detail_id,$box_code,$moved_qty)
    {
        $query1 = DB::select(DB::raw("SELECT * from wms_box_details where subloc_transfer_id = '$picklist_detail_id' and box_code='$box_code' "));
         if(count($query1)==0)
        {
            $query = DB::select(DB::raw("INSERT INTO wms_box_details (`subloc_transfer_id`, `box_code`, `moved_qty`) VALUES('$picklist_detail_id','$box_code','$moved_qty')"));
        }
        else  {
             $query = DB::select(DB::raw("UPDATE wms_box_details  set  moved_qty='$moved_qty' WHERE subloc_transfer_id='$picklist_detail_id' and box_code='$box_code'"));
        }

        return $query;

    }
     public static function ApiPTLBoxUpdate($move_doc, $box_code)
    {

        $query = DB::select(DB::raw("SELECT * from wms_box where move_doc_number='$move_doc' and box_code='$box_code'"));
        return $query;

    }
     public static function ApiPSTTLBoxUpdate($move_doc, $box_code)
    {

        $query = DB::select(DB::raw("SELECT * from wms_box where tl_number='$move_doc' and box_code='$box_code'"));
        return $query;

    }
        public static function ApiPTLBoxValidate($box_code)
    {

        $query = DB::select(DB::raw(" SELECT count(box_code) as boxes_count from wms_box
        where box_code='$box_code'"));
        return $query;

    }
    public static function ApiLBlist($piler_id)
    {
        $query=DB::SELECT(DB::raw(" SELECT load_code FROM wms_load where assigned_to_user_id= '$piler_id' and is_shipped='0' and data_value = '1' and tagging_load='1'"));
        return $query;

    }
    public static function ApiSTLBlist($piler_id)
    {
        $query=DB::SELECT(DB::raw(" SELECT load_code FROM wms_load where assigned_to_user_id= '$piler_id' and is_shipped='0' and data_value = '1' and tagging_load='2'"));
        return $query;

    }
    public static function ApigetLoadinglist($piler_id)
    {
        $query=DB::SELECT(DB::raw(" SELECT load_code FROM wms_load where assigned_to_user_id= '$piler_id' and is_shipped='0' and data_value = '1' and tagging_load='1'"));
        return $query;

    }
    public static function ApigetLoadingSTlist($piler_id)
    {
        $query=DB::SELECT(DB::raw(" SELECT load_code FROM wms_load where assigned_to_user_id= '$piler_id' and is_shipped='0' and data_value = '1' and tagging_load='2'"));
        return $query;

    }
    public static function ApigetLoadinglistDetails($load_code)
    {
        $query=DB::SELECT(DB::raw(" SELECT load_code, box_number from wms_load_details where load_code
            ='$load_code'"));
        return $query;

    }
    public static function ApigetLoadingSTlistDetails($load_code)
    {
        $query=DB::SELECT(DB::raw(" SELECT load_code, box_number from wms_load_details where load_code
            ='$load_code'"));
        return $query;

    }

    public static function ApigetUpdateNewLoadingBoxStatus($load_code, $box_code, $status)
    {
        $query=DB::SELECT(DB::raw("UPDATE wms_load_details set is_load='$status' where load_code='$load_code' and box_number='$box_code'"));
        return $query;

    }
    public static function ApigetUpdateNewLoadingSTBoxStatus($load_code, $box_code, $status)
    {
        $query=DB::SELECT(DB::raw("UPDATE wms_load_details set is_load='$status' where load_code='$load_code' and box_number='$box_code'"));
        return $query;

    }
    
    
     public static function ApiLBlistdetails($load_code)
    {
       
        $query=DB::SELECT(DB::raw("SELECT wms_load_details.load_code, wms_load_details.move_doc_number, wms_picklist_details.store_code, wms_stores.store_name FROM wms_load_details left join wms_picklist_details on wms_load_details.move_doc_number = wms_picklist_details.move_doc_number left join wms_stores on wms_picklist_details.store_code = wms_stores.store_code
            WHERE  load_code='$load_code' group by wms_picklist_details.move_doc_number "));

            return $query;
    }
     public static function ApiSTLBlistdetails($load_code)
    {
       
        $query=DB::SELECT(DB::raw("SELECT wms_load_details.load_code, wms_load_details.move_doc_number, wms_store_return_pick_details.from_store_code, wms_store_return_pick_details.to_store_code as store_name
FROM wms_load_details 
left join wms_store_return_pick_details on wms_load_details.move_doc_number = wms_store_return_pick_details.move_doc_number 
left join wms_stores on wms_store_return_pick_details.from_store_code = wms_stores.store_code
WHERE  load_code='$load_code' group by wms_store_return_pick_details.move_doc_number "));

            return $query;
    }
     public static function ApiSTLBlistdetailsBox($move_doc)
    {
       
        $query=DB::SELECT(DB::raw("SELECT wms_box.box_code, wms_box.store_code, wms_box.tl_number from wms_box where tl_number='$move_doc'"));

            return $query;
    }
    public static function ApiLBListDetailbox($move_doc)
    {
         $query=DB::SELECT(DB::raw("SELECT wms_box.box_code, wms_box.store_code, wms_box.move_doc_number from wms_box where move_doc_number='$move_doc'"));

            return $query;
    } 
    public static function ApiUpdateLoadingStatus($load_code,$date)
    {
         $query=DB::SELECT(DB::raw("UPDATE wms_load set ship_at='$date', is_shipped='1' where load_code='$load_code' "));

            return $query;
    }
    public static function ApiUpdateLoadingSTStatus($load_code,$date)
    {
         $query=DB::SELECT(DB::raw("UPDATE wms_load set ship_at='$date', is_shipped='1' where load_code='$load_code' "));

            return $query;
    }
    public static function getApiSOPiler($piler_id)
    {
         $query=DB::SELECT(DB::raw("SELECT wms_store_order.load_code, wms_store_order.so_no as move_doc, wms_store_order.store_code from wms_store_order where wms_store_order.assigned_user_id='$piler_id' and so_status='3'"));

            return $query;
    }
     public static function getApiSOboxnumber($move_doc)
    {
         $query=DB::SELECT(DB::raw("SELECT wms_store_detail_box.box_code, wms_store_detail_box.move_doc_number 
                    from wms_store_detail_box
                    where wms_store_detail_box.move_doc_number='$move_doc' group by wms_store_detail_box.box_code"));

            return $query;
    } 
     public static function getAPIPStoreListDetailUpc($moved_doc, $box_code)
    {
         $query=DB::SELECT(DB::raw(" SELECT wms_store_detail_box.move_doc_number, wms_store_detail_box.box_code, wms_store_detail_box.upc,
wms_product_lists.description, wms_store_detail_box.quantity_packed
from wms_store_detail_box
left join wms_product_lists on wms_store_detail_box.upc = wms_product_lists.upc 
where wms_store_detail_box.move_doc_number='$moved_doc' and wms_store_detail_box.box_code= '$box_code'"));

            return $query;
    }   
    public static function getApiSOdetailContent($boxcode, $move_doc)
    {
         $query=DB::SELECT(DB::raw("SELECT wms_store_order.load_code, wms_store_order_detail.sku,  wms_store_order_detail.ordered_qty, wms_box_details.moved_qty, wms_store_order_detail.delivered_qty, wms_box.box_code, wms_product_lists.description 
            FROM wms_store_order
            inner join wms_store_order_detail on wms_store_order.so_no = wms_store_order_detail.so_no
            left join wms_box on wms_store_order.so_no = wms_box.move_doc_number
            inner join wms_box_details on wms_box.box_code = wms_box_details.box_code
            left join wms_product_lists on wms_store_order_detail.sku = wms_product_lists.upc
            where wms_box.box_code='$boxcode' and wms_store_order_detail.so_no='$move_doc'"));

            return $query;
    }   
    public static function getApiUpdateLoadingBoxStatus($move_doc, $boxcode, $status)
    {
         $query=DB::SELECT(DB::raw("UPDATE wms_box set boxstatus_unload='$status', wms_box.in_use='1' where move_doc_number='$move_doc' and box_code='$boxcode' "));

            return $query;
    }    
    public static function getApiUpdateSTLoadingBoxStatus($move_doc, $boxcode, $status)
    {
         $query=DB::SELECT(DB::raw("UPDATE wms_box set boxstatus_unload='$status' where tl_number='$move_doc' and box_code='$boxcode' "));

            return $query;
    }   
    public static function getApiUpdateStoreOrderUpc($move_doc, $upc, $rcv_qty)
    {
         $query=DB::SELECT(DB::raw("UPDATE  wms_store_order_detail set  delivered_qty='$rcv_qty' where so_no='$move_doc' and sku='$upc'"));

            return $query;
    }
    public static function getAPIUpdateStoreOrderBox($move_doc, $box_code, $upc, $rcv_qty)
    {

         $query=DB::SELECT(DB::raw("UPDATE  wms_store_detail_box set  quantity_pick='$rcv_qty' where move_doc_number='$move_doc' and box_code='$box_code' and upc='$upc'"));

            return $query;
    }
    public static function getAPIUpdateStoreOrderStatus($move_doc )
    {

         $query=DB::SELECT(DB::raw("UPDATE  wms_store_order set  so_status='4' where so_no='$move_doc' "));

            return $query;
    }
    public static function getAPIRSTList($piler_id)
    {

         $query=DB::SELECT(DB::raw("SELECT so_no as mts_no, from_store_code as location_id, store_name as location_name
                    from wms_store_return

                    left join  wms_stores on wms_store_return.from_store_code = wms_stores.store_code
                    where assigned_to_user_id='$piler_id' and so_status='21'"));

            return $query;
    }

    public static function getAPIRSTListDetail($mts_no)
    {

         $query=DB::SELECT(DB::raw("SELECT wms_store_return_detail.so_no as mts_no, wms_store_return_detail.sku as upc, wms_product_lists.description, wms_store_return_detail.delivered_qty as oqty, wms_store_return_detail.received_qty as rqty  FROM wms_store_return_detail left join wms_product_lists on wms_store_return_detail.sku = wms_product_lists.upc where so_no='$mts_no'"));

            return $query;
    }
    public static function getAPIPSTList($piler_id)
    {

         $query=DB::SELECT(DB::raw("SELECT wms_store_return_pick_details.to_store_code as location_id, wms_stores.store_name as location_name, (SELECT wms_stores.store_name from wms_stores where wms_stores.store_code=wms_store_return_pick_details.to_store_code) as from_name, wms_store_return_pickinglist.move_doc_number as mts_no, wms_store_return_pick_details.to_store_code as from_id
                from wms_store_return_pickinglist
                left join wms_store_return_pick_details on wms_store_return_pickinglist.move_doc_number = wms_store_return_pick_details.move_doc_number
                left join wms_stores on wms_store_return_pick_details.from_store_code = wms_stores.store_code
            where pl_status=16  and assigned_to_user_id='$piler_id'"));     

            return $query;

    }
    public static function getAPIPSTListDetail($mts_no)
    {

         $query=DB::SELECT(DB::raw("SELECT move_doc_number as mts_no, wms_store_return_pick_details.sku as upc, 'test' as descr, id as picking_id, quantity_to_pick as oqty, moved_qty as rqty 
            FROM wms_store_return_pick_details
            WHERE move_doc_number ='$mts_no'"));     

            return $query;
    }
    public static function getAPIRRLList($piler_id)
    {

         $query=DB::SELECT(DB::raw("SELECT wms_stores.store_name as location_name, wms_reverse_logistic.move_doc_number as mts_no, wms_reverse_logistic.from_store_code as location_id FROM wms_reverse_logistic
                left join wms_stores on wms_reverse_logistic.from_store_code = wms_stores.store_code 
                    where so_status=21 and assigned_to_user_id='$piler_id'"));     

            return $query;
    }

    public static function getAPIRSTListDetailUpdate($mts_no, $upc, $rqty)
    {

         $query=DB::SELECT(DB::raw("UPDATE wms_store_return_detail set received_qty='$rqty' where so_no='$mts_no' AND sku='$upc'" ));     

            return $query;
    }

    public static function getAPIRSTListDetailAdd($mts_no, $upc, $rqty)
    {

         $query=DB::SELECT(DB::raw("INSERT INTO `wms_store_return_detail`(`id`, `so_no`, `sku`, `delivered_qty`, `received_qty`, `sync_status`, `created_at`, `updated_at`, `jda_sync_date`) VALUES ('','$mts_no','$upc',0,'$rqty','','','','')" ));     

            return $query;
    }
    public static function getAPIRSTListUpdateStatus($mts_no)
     {
          $query=DB::SELECT(DB::raw("UPDATE wms_store_return set so_status=22 where so_no='$mts_no'"));     

            return $query;
     }
     public static function getAPIRRLListDetailUpdate($mts_no, $upc, $rqty)
     {
        $query=DB::SELECT(DB::raw("UPDATE wms_reverse_logistic_det set moved_qty='$rqty' where move_doc_number='$mts_no' and upc='$upc'"));     

            return $query;

     }
     public static function getAPIRRLListDetail($mts_no)
     {
        $query=DB::SELECT(DB::raw("SELECT move_doc_number as mts_no, wms_reverse_logistic_det.upc, delivered_qty as oqty, wms_product_lists.description, wms_reverse_logistic_det.moved_qty  as rqty FROM `wms_reverse_logistic_det` left join wms_product_lists on wms_reverse_logistic_det.upc = wms_product_lists.upc WHERE move_doc_number='$mts_no' "));     

            return $query;

     }
    public static function getAPIRRLListDetailAdd($mts_no, $upc, $rqty)
    {

         $query=DB::SELECT(DB::raw("INSERT INTO `wms_reverse_logistic_det`(`id`, `move_doc_number`, `upc`, `delivered_qty`, `moved_qty`, `created_at`, `updated_at`) VALUES ('','$mts_no','$upc',0,'$rqty','','')" ));     

            return $query;
    }
    public static function getAPIRRLListUpdateStatus($mts_no)
    {

         $query=DB::SELECT(DB::raw("UPDATE wms_reverse_logistic set so_status=22 where move_doc_number='$mts_no'" ));     

            return $query;
    }
        public static function GetApiUser($user_id,$password) 
    {
        $query = DB::select(DB::raw("SELECT id,username, 'password' as password,firstname as fname,lastname as lname from wms_users where id='$user_id' and deleted_at='0000-00-00 00:00:00'"));
            
        return $query;
    }
}
