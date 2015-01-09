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
	public static $stores = 'wms_stores';
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
	public static $storeOrderLetdown = 'wms_store_order_letdown';


	public function __construct() {
		$this->instance = new eWMSMigration();
	}

	public function products() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "product";
		$eWMSTable = self::$products;
		$columns = '(sku, upc, short_description, description, vendor, dept_code, sub_dept, class, sub_class, set_code)';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	public function slots() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "slot_master";
		$eWMSTable = self::$slots;
		$columns = '(slot_code)';

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
		$columns = '(store_code, store_name, address1, address2, address3, city)';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	public function letdown() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "letdown_header";
		$eWMSTable = self::$letdown;
		$columns = '(move_doc_number)';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	public function letdownDetail() {
		//unique index: store_or_sku, move_doc_number, from_slot_code, store_code, so_no
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "letdown_detail";
		$eWMSTable = self::$letdownDetails;
		$columns = '(@move_doc_number, @sku, @from_slot_code, @quantity_to_letdown, @store_code, @date_created, @sequence_no, @group_name)
				set move_doc_number=@move_doc_number, sku=@sku, from_slot_code=@from_slot_code,
					quantity_to_letdown=@quantity_to_letdown, store_code=@store_code,
					sequence_no=@sequence_no, group_name=@group_name, to_slot_code=@to_slot_code';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	public function picklist() {
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "picklist_header";
		$eWMSTable = self::$picklist;
		// $type = 'store';
		$columns = '(move_doc_number)';

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
		$columns = '(@move_doc_number, @sku, @from_slot_code, @quantity_to_pick, @store_code, @so_no, @date_created, @sequence_no, @group_name)
				set move_doc_number=@move_doc_number, sku=@sku, from_slot_code=@from_slot_code,
					quantity_to_pick=@quantity_to_pick, store_code=@store_code, so_no=@so_no,
					sequence_no=@sequence_no, group_name=@group_name';

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
		$columns = '(vendor_id,receiver_no,purchase_order_no,destination)';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);
		$this->instance->import($csvLocation, $eWMSTable, $columns);
	}

	public function purchaseOrderDetails() {
		//unique index: store_or_sku, move_doc_number, from_slot_code, store_code, so_no
		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "purchase_order_detail";
		$eWMSTable = self::$purchaseOrderDetails;
		$columns = '(@vendor,@receiver_no,@sku, @quantity_ordered,@unit_cost) set sku=@sku, receiver_no=@receiver_no, quantity_ordered=@quantity_ordered, unit_price=@unit_cost';

		$csvLocation = $this->instance->getLatestCsv($csvfile_pattern);

		$result = $this->instance->import($csvLocation, $eWMSTable, $columns);
		$this->instance->_setPoIds(self::$purchaseOrder, $result);
	}

	//blank
	public function storeOrder() {

		echo "\n Running method " . __METHOD__ . "\n";
		$csvfile_pattern = "store_order_header";
		$eWMSTable = self::$storeOrder;
		$columns = '(@so_no, @store_code, @so_status, @order_date) set so_no=@so_no, store_code=@store_code, order_date=@order_date';
		// so_no | store_name | so_status | order_date | created_at

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
