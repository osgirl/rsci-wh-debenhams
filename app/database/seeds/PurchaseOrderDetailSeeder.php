<?php

class PurchaseOrderDetailSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		DB::table('purchase_order_details')->truncate();

		$data = array(
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0001',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0002',
					    "quantity_ordered"=>5,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0003',
					    "quantity_ordered"=>20,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0004',
					    "quantity_ordered"=>12,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0005',
					    "quantity_ordered"=>6,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0006',
					    "quantity_ordered"=>1,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0007',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0008',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0009',
					    "quantity_ordered"=>5,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0010',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0011',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0012',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0013',
					    "quantity_ordered"=>5,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0014',
					    "quantity_ordered"=>1,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0015',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0016',
					    "quantity_ordered"=>11,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0017',
					    "quantity_ordered"=>3,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0018',
					    "quantity_ordered"=>1,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0019',
					    "quantity_ordered"=>7,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0020',
					    "quantity_ordered"=>1,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0021',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0022',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0023',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0024',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0025',
					    "quantity_ordered"=>1,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0026',
					    "quantity_ordered"=>7,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0027',
					    "quantity_ordered"=>1,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0028',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0029',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0030',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>1,
					    "sku"=>'NGM0031',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 1
					  ),
					array(
					  	"po_id"=>2,
					    "sku"=>'NGM0001',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 2
					  ),
					array(
					  	"po_id"=>2,
					    "sku"=>'NGM0023',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 2
					  ),
					array(
					  	"po_id"=>2,
					    "sku"=>'NGM0021',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 2
					  ),
					array(
					  	"po_id"=>3,
					    "sku"=>'NGM0001',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 3
					  ),
					array(
					  	"po_id"=>3,
					    "sku"=>'NGM0023',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 3
					  ),
					array(
					  	"po_id"=>3,
					    "sku"=>'NGM0021',
					    "quantity_ordered"=>10,
					    "quantity_delivered"=>0,
					    "receiver_no" => 3
					  ),
				);

		foreach ($data as $d) {
            PurchaseOrderDetail::create($d);
        }

	}

}