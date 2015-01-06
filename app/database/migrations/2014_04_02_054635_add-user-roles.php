<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserRoles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('user_roles')->insert(array(
			'role_name' => 'superadmin',
			'permissions' => '["CanAccessPurchaseOrders","CanExportPurchaseOrders","CanSyncPurchaseOrders","CanAssignPurchaseOrders","CanClosePurchaseOrders","CanAccessPurchaseOrderDetails","CanExportPurchaseOrderDetails","CanSyncPurchaseOrderDetails","CanAssignPurchaseOrderDetails","CanClosePurchaseOrderDetails","CanAccessStoreOrders","CanExportStoreOrders","CanSyncStoreOrders","CanCloseStoreOrders","CanGenerateLetdownStoreOrders","CanAccessStoreOrderDetails","CanExportStoreOrderDetails","CanSyncStoreOrderDetails","CanCloseStoreOrderDetails","CanGenerateLetdownStoreOrderDetails","CanAccessInventory","CanExportInventory","CanSyncInventory","CanAccessInventoryDetails","CanExportInventoryDetails","CanSyncInventoryDetails","CanAccessProductMasterList","CanExportProductMasterList","CanAccessSlotMasterList","CanExportSlotMasterList","CanAccessVendorMasterList","CanExportVendorMasterList","CanAccessUsers","CanInsertUsers","CanUpdateUsers","CanDeleteUsers","CanExportUsers","CanChangePasswordUsers","CanAccessUserRoles","CanInsertUserRoles","CanUpdateUserRoles","CanDeleteUserRoles","CanAccessAuditTrail","CanExportAuditTrail","CanArchiveAuditTrail","CanAccessSettings","CanInsertSettings","CanUpdateSettings","CanDeleteSettings","CanAccessStockPiler", "CanAccessLetDowns","CanAccessLetDownDetails", "CanCloseLetDown", "CanCloseLetDownDetails", "CanViewLetdownLockTags","CanUnlockLetDown", "CanExportLetDowns", "CanExportLetDownDetails", "CanSyncBoxManifest", "CanAccessBoxCreation", "CanCreateBox", "CanExportBoxes", "CanDeleteBoxes", "CanEditBoxes", "CanAccessPicking", "CanAccessPickingDetails" ,"CanExportPickingDocuments", "CanExportPickingDetails", "CanLoadPicking", "CanViewPickingLockTags", "CanUnlockPicking", "CanEditPicklist", "CanAddLoad", "CanShipLoad", "CanAccessLoad", "CanExportLoads"]',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		));

		DB::table('user_roles')->insert(array(
			'role_name' => 'doc_clerk',
			'permissions' => '["CanAccessPurchaseOrders","CanExportPurchaseOrders","CanSyncPurchaseOrders","CanAssignPurchaseOrders","CanClosePurchaseOrders","CanAccessPurchaseOrderDetails","CanExportPurchaseOrderDetails","CanSyncPurchaseOrderDetails","CanAssignPurchaseOrderDetails","CanClosePurchaseOrderDetails","CanAccessStoreOrders","CanExportStoreOrders","CanSyncStoreOrders","CanCloseStoreOrders","CanGenerateLetdownStoreOrders","CanAccessStoreOrderDetails","CanExportStoreOrderDetails","CanSyncStoreOrderDetails","CanCloseStoreOrderDetails","CanGenerateLetdownStoreOrderDetails","CanAccessInventory","CanExportInventory","CanSyncInventory","CanAccessInventoryDetails","CanExportInventoryDetails","CanSyncInventoryDetails","CanAccessProductMasterList","CanExportProductMasterList","CanAccessSlotMasterList","CanExportSlotMasterList","CanAccessVendorMasterList","CanExportVendorMasterList","CanAccessUsers","CanInsertUsers","CanUpdateUsers","CanDeleteUsers","CanExportUsers","CanChangePasswordUsers","CanAccessUserRoles","CanInsertUserRoles","CanUpdateUserRoles","CanDeleteUserRoles","CanAccessAuditTrail","CanExportAuditTrail","CanArchiveAuditTrail","CanAccessSettings","CanInsertSettings","CanUpdateSettings","CanDeleteSettings",  "CanAccessLetDowns", "CanAccessLetDownDetails", "CanCloseLetDown","CanCloseLetDownDetails","CanViewLetdownLockTags","CanUnlockLetDown", "CanExportLetDowns", "CanExportLetDownDetails", "CanSyncBoxManifest",  "CanAccessBoxCreation", "CanCreateBox", "CanExportBoxes", "CanDeleteBoxes", "CanEditBoxes","CanAccessPicking", "CanAccessPickingDetails" ,"CanExportPickingDocuments", "CanExportPickingDetails", "CanLoadPicking", "CanViewPickingLockTags", "CanUnlockPicking", "CanEditPicklist", "CanAddLoad", "CanShipLoad", "CanAccessLoad", "CanExportLoads"]',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		));

		DB::table('user_roles')->insert(array(
			'role_name' => 'stock_piler',
			'permissions' => '["CanAccessStockPiler"]',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		));


		DB::table('user_roles')->insert(array(
			'role_name' => 'store_owner',
			'permissions' => '["CanAccessStockPiler"]',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		));

		DB::table('user_roles')->insert(array(
			'role_name' => 'box_creator',
			'permissions' => '["CanAccessBoxCreation", "CanCreateBox", "CanExportBoxes", "CanDeleteBoxes", "CanEditBoxes"]',
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('user_roles')->where('role_name', '=', 'superadmin')->delete();
		DB::table('user_roles')->where('role_name', '=', 'admin')->delete();
		DB::table('user_roles')->where('role_name', '=', 'stock_piler')->delete();
		DB::table('user_roles')->where('role_name', '=', 'store_owner')->delete();
		DB::table('user_roles')->where('role_name', '=', 'box_creator')->delete();
	}

}
