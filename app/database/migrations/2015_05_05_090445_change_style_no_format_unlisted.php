<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeStyleNoFormatUnlisted extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//add new column
        Schema::table('unlisted', function($table) {
        	$table->string('style_no_new',128)->after('style_no');
        });
        //copy data from old column to new
        $unlistedItems = Unlisted::all();
	    if ($unlistedItems) {
	        foreach ($unlistedItems as $unlistedItem) {
	            $u = Unlisted::find($unlistedItem->id);

	            $u->style_no_new = $u->style_no;

	            $u->save();
	        }
	    }
		//drop old column
        Schema::table('unlisted', function($table) {
        	$table->dropColumn('style_no');
        });
		//rename new column
        Schema::table('unlisted', function($table) {
        	$table->renameColumn('style_no_new', 'style_no');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('unlisted', function($table)
	    {
	        // 1- rename the existing column
	        $table->renameColumn('style_no', 'style_no_old');
	    });
	    Schema::table('unlisted', function($table)
	    {
	        // 2- add a new column with the desired data type to the table
	        // and give it a name matches name of the existing column before renaming
	        // note that after() method is used to order the column and works only with MySQL
	        $table->string('style_no', 30)->after('style_no_old');
	    });
	    //copy data from old column to new
        $unlistedItems = Unlisted::all();
	    if ($unlistedItems) {
	        foreach ($unlistedItems as $unlistedItem) {
	            $u = Unlisted::find($unlistedItem->id);

	            $u->style_no = $u->style_no_old;

	            $u->save();
	        }
	    }
		Schema::table('unlisted', function($table)
	    {
	        // 1- rename the existing column
	        $table->dropColumn('style_no_old');
	    });
	}

}
