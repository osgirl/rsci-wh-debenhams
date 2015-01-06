<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateIndexes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		

		Schema::table('audit_trail', function($table)
		{
		   if (Schema::hasColumn('audit_trail', 'user_id'))
			{
			    $table->index('user_id');
			}
		});

		Schema::table('department', function($table)
		{
		    if ((Schema::hasColumn('department', 'sub_dept')) && (Schema::hasColumn('department', 'class')) && (Schema::hasColumn('department', 'sub_class')))
			{
			    $table->index(array('sub_dept', 'class', 'sub_class'));
			}

			if ((Schema::hasColumn('department', 'dept_code')) && (Schema::hasColumn('department', 'sub_dept')) && (Schema::hasColumn('department', 'class')) && (Schema::hasColumn('department', 'sub_class')))
			{
			    $table->index(array('dept_code', 'sub_dept', 'class','sub_class'));
			}
		});
		

		Schema::table('settings', function($table)
		{
		    if (Schema::hasColumn('settings', 'deleted_at'))
			{
			    $table->index('deleted_at');
			}

		});

		Schema::table('slot_details', function($table)
		{
		    if ((Schema::hasColumn('slot_details', 'sku')) && (Schema::hasColumn('slot_details', 'slot_id')))
			{
			    $table->index(array('sku', 'slot_id'));
			}

			if (Schema::hasColumn('slot_details', 'deleted_at'))
			{
			    $table->index('deleted_at');
			}
		});


		Schema::table('users', function($table)
		{
			if (Schema::hasColumn('users', 'role_id'))
			{
			    $table->index('role_id');
			}

			if (Schema::hasColumn('users', 'deleted_at'))
			{
			    $table->index('deleted_at');
			}

			if (Schema::hasColumn('users', 'brand'))
			{
			    $table->index('brand');
			}

			if (Schema::hasColumn('users', 'barcode'))
			{
			    $table->index('barcode');
			}

			if ((Schema::hasColumn('users', 'username')) && (Schema::hasColumn('users', 'password')))
			{
			    $table->index(array('username', 'password'));
			}
		});

		Schema::table('user_roles', function($table)
		{
			if (Schema::hasColumn('user_roles', 'role_name'))
			{
			    $table->index('role_name');
			}

			if (Schema::hasColumn('user_roles', 'deleted_at'))
			{
			    $table->index('deleted_at');
			}
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{	

		/*
	
		Schema::table('audit_trail', function($table)
		{
			$table->dropIndex('audit_trail_user_id_index');
		});

		Schema::table('department', function($table)
		{ 
			$table->dropIndex('department_sub_dept_class_sub_class_index');
			$table->dropIndex('department_dept_code_sub_dept_class_sub_class_index');
		});


		Schema::table('settings', function($table)
		{
		    $table->dropIndex('settings_deleted_at_index');

		});


		Schema::table('slot_details', function($table)
		{
			$table->dropIndex('slot_details_sku_slot_id_index');
			$table->dropIndex('slot_details_deleted_at_index');
		});


		Schema::table('users', function($table)
		{
			$table->dropIndex('users_role_id_index');
			$table->dropIndex('users_deleted_at_index');
			$table->dropIndex('users_brand_index');
			$table->dropIndex('users_barcode_index');
			$table->dropIndex('users_username_password_index');
		});

		Schema::table('user_roles', function($table)
		{
			$table->dropIndex('user_roles_role_name_index');
			$table->dropIndex('user_roles_deleted_at_index');
		});*/

	}

}


// user_id
// checking if 0 
	//if 0 assign
	//if >0 check if user_id = assigned user_id
		//if not equals, throw Someone is already working on this
		//else diretso lang
	//else if 
