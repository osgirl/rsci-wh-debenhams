<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddErrorMessageColumnTransactionsToJda extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('transactions_to_jda', function($table)
        {
            $table->string('error_message');
        });
        Schema::table('store_order_detail', function($table)
        {
            $table->string('error_message');
        });
        Schema::table('store_order', function($table)
        {
            $table->string('error_message');
        });
        Schema::table('slot_details', function($table)
        {
            $table->string('error_message');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('transactions_to_jda', function($table)
        {
            $table->dropColumn('error_message');
        });
        Schema::table('store_order_detail', function($table)
        {
            $table->dropColumn('error_message');
        });
        Schema::table('store_order', function($table)
        {
            $table->dropColumn('error_message');
        });
        Schema::table('slot_details', function($table)
        {
            $table->dropColumn('error_message');
        });
	}

}
