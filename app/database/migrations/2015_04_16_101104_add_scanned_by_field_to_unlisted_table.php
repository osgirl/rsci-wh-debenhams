<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddScannedByFieldToUnlistedTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('unlisted', function(Blueprint $table)
        {
            $table->tinyInteger('scanned_by')->after('division');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('unlisted', function(Blueprint $table)
        {
            $table->dropColumn('scanned_by');
        });
	}

}
