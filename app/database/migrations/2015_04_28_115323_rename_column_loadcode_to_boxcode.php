<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnLoadcodeToBoxcode extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('inter_transfer', function($table) {
            $table->renameColumn('load_code', 'box_code');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
	 	Schema::table('inter_transfer', function($table) {
            $table->renameColumn('box_code', 'load_code');
        });
	}

}
