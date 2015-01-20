<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatasetEntriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		/* Purchase order status type */
		DB::table('dataset')->insert(array(
			'data_code' => 'PO_STATUS_TYPE',
			'data_value' => 'open',
			'data_display' => 'Open',
			'description' => 'Purchase order status is open',
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('dataset')->insert(array(
			'data_code' => 'PO_STATUS_TYPE',
			'data_value' => 'in_process',
			'data_display' => 'In Process',
			'description' => 'Purchase order status is In-process',
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('dataset')->insert(array(
			'data_code' => 'PO_STATUS_TYPE',
			'data_value' => 'done',
			'data_display' => 'Done',
			'description' => 'Purchase order status is done',
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('dataset')->insert(array(
			'data_code' => 'PO_STATUS_TYPE',
			'data_value' => 'closed',
			'data_display' => 'Posted',
			'description' => 'Purchase order status is now closed',
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('dataset')->insert(array(
			'data_code' => 'ZONE_TYPE',
			'data_value' => 'moved_to_reserve',
			'data_display' => 'Moved to reserve zone',
			'description' => 'Particular upc/sku will be move to resever zone',
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('dataset')->insert(array(
			'data_code' => 'ZONE_TYPE',
			'data_value' => 'moved_to_picklist',
			'data_display' => 'Moved to picklist zone',
			'description' => 'Particular upc/sku will be move to picklist zone',
			'created_at' => date('Y-m-d H:i:s')
		));


		DB::table('dataset')->insert(array(
			'data_code' => 'LETDOWN_STATUS_TYPE',
			'data_value' => '0',
			'data_display' => 'Unmoved',
			'description' => 'Letdown document is still in reserved zone.',
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('dataset')->insert(array(
			'data_code' => 'LETDOWN_STATUS_TYPE',
			'data_value' => '1',
			'data_display' => 'Moved',
			'description' => 'Letdown document is moved to picking zone',
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('dataset')->insert(array(
			'data_code' => 'LETDOWN_STATUS_TYPE',
			'data_value' => '2',
			'data_display' => 'Closed',
			'description' => 'Letdown document status is now closed',
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('dataset')->insert(array(
			'data_code' => 'PICKLIST_STATUS_TYPE',
			'data_value' => '0',
			'data_display' => 'Unmoved',
			'description' => 'Picklist document is still in reserved zone.',
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('dataset')->insert(array(
			'data_code' => 'PICKLIST_STATUS_TYPE',
			'data_value' => '1',
			'data_display' => 'Moved',
			'description' => 'Picklist document is moved to picking zone',
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('dataset')->insert(array(
			'data_code' => 'PICKLIST_STATUS_TYPE',
			'data_value' => '2',
			'data_display' => 'Closed',
			'description' => 'Picklist document status is now closed',
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('dataset')->insert(array(
			'data_code' => 'SO_STATUS_TYPE',
			'data_value' => '1',
			'data_display' => 'Open',
			'description' => 'Store Order is still open.',
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('dataset')->insert(array(
			'data_code' => 'SO_STATUS_TYPE',
			'data_value' => '2',
			'data_display' => 'Done',
			'description' => 'Store Order is done.',
			'created_at' => date('Y-m-d H:i:s')
		));

		DB::table('dataset')->insert(array(
			'data_code' => 'SO_STATUS_TYPE',
			'data_value' => '3',
			'data_display' => 'Closed',
			'description' => 'Store Order is Closed.',
			'created_at' => date('Y-m-d H:i:s')
		));
		/* End of purchase order type */
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('dataset')->truncate();
	}

}
