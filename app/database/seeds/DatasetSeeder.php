<?php

class DatasetSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {

		Dataset::truncate();

		$data = array(
					array(
						'data_code' => 'PO_STATUS_TYPE',
						'data_value' => 'open',
						'data_display' => 'Open',
						'description' => 'Purchase order status is open',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'PO_STATUS_TYPE',
						'data_value' => 'assigned',
						'data_display' => 'Assigned',
						'description' => 'Purchase order status is assigned to stock piler/s',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'PO_STATUS_TYPE',
						'data_value' => 'in_process',
						'data_display' => 'In Process',
						'description' => 'Purchase order status is In-process',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'PO_STATUS_TYPE',
						'data_value' => 'done',
						'data_display' => 'Done',
						'description' => 'Purchase order status is done',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'PO_STATUS_TYPE',
						'data_value' => 'closed',
						'data_display' => 'Posted',
						'description' => 'Purchase order status is now closed',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'ZONE_TYPE',
						'data_value' => 'moved_to_reserve',
						'data_display' => 'Moved to reserve zone',
						'description' => 'Particular upc/sku will be move to resever zone',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'ZONE_TYPE',
						'data_value' => 'moved_to_picklist',
						'data_display' => 'Moved to picklist zone',
						'description' => 'Particular upc/sku will be move to picklist zone',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'LETDOWN_STATUS_TYPE',
						'data_value' => '0',
						'data_display' => 'Unmoved',
						'description' => 'Letdown document is still in reserved zone.',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'LETDOWN_STATUS_TYPE',
						'data_value' => '1',
						'data_display' => 'Moved',
						'description' => 'Letdown document is moved to picking zone',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'LETDOWN_STATUS_TYPE',
						'data_value' => '2',
						'data_display' => 'Closed',
						'description' => 'Letdown document status is now closed',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'SO_STATUS_TYPE',
						'data_value' => '1',
						'data_display' => 'Open',
						'description' => 'Store Order is still open.',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'SO_STATUS_TYPE',
						'data_value' => '2',
						'data_display' => 'Done',
						'description' => 'Store Order is done.',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'SO_STATUS_TYPE',
						'data_value' => '3',
						'data_display' => 'Closed',
						'description' => 'Store Order is Closed.',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
					  	'data_code' => 'PICKLIST_STATUS_TYPE',
						'data_value' => 'open',
						'data_display' => 'Open',
						'description' => 'Picklist is open',
						'created_at' => date('Y-m-d H:i:s')
					  ),
					array(
					  	'data_code' => 'PICKLIST_STATUS_TYPE',
						'data_value' => 'assigned',
						'data_display' => 'Assigned',
						'description' => 'Picklist status is assigned to stock piler/s',
						'created_at' => date('Y-m-d H:i:s')
					  ),
					array(
						'data_code' => 'PICKLIST_STATUS_TYPE',
						'data_value' => 'in_process',
						'data_display' => 'In Process',
						'description' => 'Picklist status is In-process',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'PICKLIST_STATUS_TYPE',
						'data_value' => 'done',
						'data_display' => 'Done',
						'description' => 'Picklist status is done',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'PICKLIST_STATUS_TYPE',
						'data_value' => 'closed',
						'data_display' => 'Posted',
						'description' => 'Picklist status is now closed',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'SR_STATUS_TYPE', // store return
						'data_value' => 'open',
						'data_display' => 'Open',
						'description' => 'Store return status is open',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'SR_STATUS_TYPE',
						'data_value' => 'assigned',
						'data_display' => 'Assigned',
						'description' => 'Store return status is assigned to stock piler/s',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'SR_STATUS_TYPE',
						'data_value' => 'in_process',
						'data_display' => 'In Process',
						'description' => 'Store return status is In-process',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'SR_STATUS_TYPE',
						'data_value' => 'done',
						'data_display' => 'Done',
						'description' => 'Store return status is done',
						'created_at' => date('Y-m-d H:i:s')
					),
					array(
						'data_code' => 'SR_STATUS_TYPE',
						'data_value' => 'closed',
						'data_display' => 'Posted',
						'description' => 'Store return status is now closed',
						'created_at' => date('Y-m-d H:i:s')
					),
			);

		foreach ($data as $d) {
            Dataset::create($d);
        }


	}

}