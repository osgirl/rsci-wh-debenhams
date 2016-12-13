<?php

// include_once("ewms_connection.php");
chdir(dirname(__FILE__));
include_once('ewms_connection.php');

class cronEWMS {

	var $instance;

	//eWMS TABLES
	public static $products = 'wms_product_lists';
	public static $slots = 'wms_slot_lists';
	public static $vendors = 'wms_vendors';
	public static $stores = 'wms_store_detail_box';
	public static $department = 'wms_department';
	public static $slotDetails = 'wms_slot_details';
	public static $inventory = 'wms_inventory';
	public static $purchaseOrder = 'wms_purchase_order_lists';
	public static $purchaseOrderDetails = 'wms_purchase_order_details';
	public static $letdown = 'wms_letdown';
	public static $letdownDetails = 'wms_letdown_details';
	public static $picklist = 'wms_picklist';
	public static $picklistDetails = 'wms_picklist_details';
	public static $storeOrder = 'wms_store_order';
	public static $storeOrderDetails = 'wms_store_order_detail';
	public static $storeReturn = 'wms_store_return';
	public static $storeReturnDetail = 'wms_store_return_detail';
	public static $storeReturn_pick = 'wms_store_return_pickinglist';
	public static $storeReturnDetail_pick = 'wms_store_return_pick_details';
	public static $reverse_logistic = 'wms_reverse_logistic';
	public static $storeReturnDetail_return = 'wms_reverse_logistic_det';
	public static $storeOrderLetdown = 'wms_store_order_letdown';


	public function __construct() {
		$this->instance = new eWMSMigration();
	}
	public function products() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "product";
		$eWMSTable = self::$products;
		$columns = '(@sku, @upc, @short_description, @description, @vendor, @dept_code, @sub_dept, @class, @sub_class, @set_code)
			SET sku=@sku, upc=@upc, short_description=@short_description, description=@description, vendor=@vendor, dept_code=@dept_code, sub_dept=@sub_dept, class=@class, sub_class=@sub_class, set_code=@set_code ';
 
		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}
 
	public function slots() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "slot_master";
		$eWMSTable = self::$slots;
		$columns = '(@slot_code,  @zone_code,@store_code) set slot_code=@slot_code, zone_code=@zone_code, store_code=@store_code';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	public function vendors() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "vendor_master";
		$eWMSTable = self::$vendors;
		$columns = '(vendor_code, vendor_name)';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	public function department() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "department";
		$eWMSTable = self::$department;
		$columns = '(@dept_code, @sub_dept, @class, @sub_class, @description)
				set dept_code=@dept_code, sub_dept=@sub_dept, class=@class, sub_class=@sub_class, description=@description';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	public function stores() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "store_master";
		$eWMSTable = self::$stores;
		$columns = '(@store_code, @store_name, @address1, @address2, @address3, @city ) set store_code=@store_code, store_name=@store_name, address1=@address1, address2=@address2, address3=@address3, city=@city';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}



	public function picklist() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "picklist_header";
		$eWMSTable = self::$picklist;
		// $type = 'store';
		$columns = '(@move_doc_number,@ship_date) set move_doc_number=@move_doc_number, ship_date=@ship_date';

		// WHMOVE
		// move_doc_number

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	public function picklistDetail() {
		//unique index: store_or_sku, move_doc_number, from_slot_code, store_code, so_no
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "picklist_detail";
		$eWMSTable = self::$picklistDetails;
		$columns = '(  @move_doc_number, @sku, @from_slot_code, @quantity_to_pick, @store_code, @created_at )
				set  move_doc_number=@move_doc_number, sku=@sku, from_slot_code=@from_slot_code,
					quantity_to_pick=@quantity_to_pick, store_code=@store_code, created_at=@created_at';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	/*public function storeOrderLetdown() {
		//unique index: store_or_sku, move_doc_number, from_slot_code, store_code, so_no
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "picklist_detail";
		$eWMSTable = self::$storeOrderLetdown;
		$columns = '(@move_doc_number, @sku, @from_slot_code, @quantity_to_pick, @store_code, @so_no) set so_no=@so_no, move_doc_number=@move_doc_number';

		// $this->instance->mysqlDump($eWMSTable);
		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}*/

	public function purchaseOrder() {
		//unique index: store_or_sku, move_doc_number, from_slot_code, store_code, so_no
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "purchase_order_header";
		$eWMSTable = self::$purchaseOrder;
		$columns = '( @receiver_no, @invoice_no, @purchase_order_no, @total_qty,   @entry_date)
				set   receiver_no=@receiver_no, invoice_no=@invoice_no, purchase_order_no=@purchase_order_no,    
					total_qty=@total_qty,  entry_date=@entry_date';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	public function purchaseOrderDetails() {
		//unique index: store_or_sku, move_doc_number, from_slot_code, store_code, so_no
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "purchase_order_detail";
		$eWMSTable = self::$purchaseOrderDetails;
		$columns = '(@sku, @upc, @receiver_no, @dept_number, @quantity_ordered,@dept_name) set sku=@sku, upc=@upc, receiver_no=@receiver_no,dept_number=@dept_number,quantity_ordered=@quantity_ordered,division=@dept_name,po_status="1"' ;
		
		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		 $this->instance->import($csvLocation, $eWMSTable, $columns);
		// $this->instance->_setPoIds(self::$ purchaseOrder, $ result);
	}

	//blank
	public function storeOrder() {

			echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "store_master_test";
		$eWMSTable = self::$stores;
		$columns = '(@store_code, @store_name, @address1, @address2, @address3, @city ) set store_code=@store_code, store_name=@store_name, address1=@address1, address2=@address2, address3=@address3, city=@city';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	//blank
	public function storeOrderDetails() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "store_order_detail";
		$eWMSTable = self::$storeOrderDetails;
		// $columns = '(so_no, sku, ordered_qty, allocated_qty)';
		$columns = '(so_no, sku, ordered_qty)';
		// so_no | sku | ordered_qty | alloctated_qty | created_at

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	//blank
	public function storeReturn() {

		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "store_return_header";
		$eWMSTable = self::$storeReturn;
		$columns = '(@so_no, @from_store_code, @to_store_code) set so_no=@so_no, from_store_code=@from_store_code, to_store_code=@to_store_code';
		// so_no | store_name | so_status | order_date | created_at

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	//blank
	public function storeReturnDetail() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "store_return_detail";
		$eWMSTable = self::$storeReturnDetail;
		$columns = '(@so_no, @sku, @delivered_qty) set so_no=@so_no, sku=@sku, delivered_qty=@delivered_qty';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	//blank
	public function storeReturn_pick() {

		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "store_return_pickinglist";
		$eWMSTable = self::$storeReturn_pick;
		$columns = '(@so_no) set move_doc_number=@so_no';
		// so_no | store_name | so_status | order_date | created_at

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	//blank
	public function storeReturnDetail_pick() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "store_return_pick_details";
		$eWMSTable = self::$storeReturnDetail_pick;
		$columns = '(@so_no, @to_store_code, @upc, @from_store_code, @quantity_to_pick) set move_doc_number=@so_no, to_store_code=@to_store_code, sku=@upc, from_store_code=@from_store_code, quantity_to_pick=@quantity_to_pick';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}
	//blank
	public function storeReturn_return() {

		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "reverse_logistic";
		$eWMSTable = self::$reverse_logistic;
		$columns = '(@so_no, @from_store_code) set move_doc_number=@so_no,  from_store_code=@from_store_code';
		// so_no | store_name | so_status | order_date | created_at

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	//blank
	public function storeReturnDetail_return() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "reverse_logistic_det";
		$eWMSTable = self::$storeReturnDetail_return;
		$columns = '(@so_no,  @upc, @delivered_qty) set move_doc_number=@so_no,  upc=@upc, delivered_qty=@delivered_qty';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	//blank
	public function inventory() {
		//unique index: store_or_sku, move_doc_number, from_slot_code, store_code, so_no
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "inventory";
		$eWMSTable = self::$inventory;
		$columns = '(@sku,@quantity_on_hand,@quantity_committed,@slot_id) set sku=@sku, quantity_on_hand=@quantity_on_hand, quantity_committed=@quantity_committed, slot_id=@slot_id';
		// vendor_id | receiver_no | purchase_order_no | destination | po_status

		// $this->instance->mysqlDump($eWMSTable); //back up inventory on the ewms
		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns, TRUE);
		$this->instance->_resetPrimaryId($eWMSTable);//reset primary key
	}

	public function close() {
		$this->instance->close();
	}

}
