<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	// protected $deleted_at = array('deleted_at');

	public static $rules = array(
	    'firstname'=>'required|min:2',
	    'lastname'=>'required|min:2',
	    // 'email'=>'required|email|unique:users',
	    'username'=>'required',
	    'password'=>'required|alpha_num|between:6,12|confirmed',
	    'password_confirmation'=>'required|alpha_num|between:6,12'
    );

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	public function getRememberToken()
	{
	    return $this->remember_token;
	}

	public function setRememberToken($value)
	{
	    $this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
	    return 'remember_token';
	}

	public static function getUserRoleByLists($role_id) {
		$result = User::where("role_id", "=", $role_id)->get()->lists('firstname', 'id');

		return $result;
	}

	public static function getUserLists() {
		$result = User::get()->lists('username', 'id');

		return $result;
	}

	public static function addUser($data = array()) {
		DB::table('users')->insert($data);
	}

	public static function updateUser($id, $data = array()) {
		$query = DB::table('users')->where('id', '=', $id);
		$query->update($data);
	}

	public static function deleteUser($data = array()) {
		foreach ($data as $item) {
			$query = DB::table('users')->where('id', '=', $item);
			$query->update(array('deleted_at' => date('Y-m-d H:i:s')));
		}
	}

	public static function getUsers($data = array()) {
		$query = DB::table('users')->select(DB::raw('wms_users.*, CONCAT(wms_users.firstname, \' \', wms_users.lastname) AS name, wms_user_roles.role_name, brand_name'))
								   ->join('user_roles', 'users.role_id', '=', 'user_roles.id', 'LEFT')
								   // ->join('settings', 'users.brand', '=', 'settings.id', 'LEFT')
								   ->join('brands', 'users.brand_id', '=', 'brands.id', 'LEFT')
								   ->where('users.id', '!=', 1)
								   ->where(function($query_sub)
							            {
							                $query_sub->where('users.deleted_at', '=', '0000-00-00 00:00:00')
								   				  	  ->orWhere('users.deleted_at', '=', NULL);
							            });

		if( CommonHelper::hasValue($data['filter_username']) ) $query->where('username', 'LIKE', '%'.$data['filter_username'].'%');
		if( CommonHelper::hasValue($data['filter_barcode']) ) $query->where('barcode', 'LIKE', '%'.$data['filter_barcode'].'%');
		if( CommonHelper::hasValue($data['filter_user_role']) ) $query->where('user_roles.id', '=', $data['filter_user_role']);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='name') $data['sort'] = 'users.firstname';
			if ($data['sort']=='role') $data['sort'] = 'user_roles.role_name';
			if ($data['sort']=='brand') $data['sort'] = 'brands.brand_name';
			if ($data['sort']=='date') $data['sort'] = 'users.created_at';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->get();

		return $result;
	}

	public static function getCountUsers($data = array()) {
		$query = DB::table('users')->select(DB::raw('wms_users.*, CONCAT(wms_users.firstname, \' \', wms_users.lastname) AS name, wms_user_roles.role_name, brand_name'))
								   ->join('user_roles', 'users.role_id', '=', 'user_roles.id', 'LEFT')
								   // ->join('settings', 'users.brand', '=', 'settings.id', 'LEFT')
								   ->join('brands', 'users.brand_id', '=', 'brands.id', 'LEFT')
								   ->where('users.id', '!=', 1)
								   ->where(function($query_sub)
							            {
							                $query_sub->where('users.deleted_at', '=', '0000-00-00 00:00:00')
								   				  	  ->orWhere('users.deleted_at', '=', NULL);
							            });

		if( CommonHelper::hasValue($data['filter_username']) ) $query->where('username', 'LIKE', '%'.$data['filter_username'].'%');
		if( CommonHelper::hasValue($data['filter_barcode']) ) $query->where('barcode', 'LIKE', '%'.$data['filter_barcode'].'%');
		if( CommonHelper::hasValue($data['filter_user_role']) ) $query->where('user_roles.id', '=', $data['filter_user_role']);

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='name') $data['sort'] = 'users.firstname';
			if ($data['sort']=='role') $data['sort'] = 'user_roles.role_name';
			if ($data['sort']=='brand') $data['sort'] = 'brands.brand_name';
			if ($data['sort']=='date') $data['sort'] = 'users.created_at';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page']))  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		$result = $query->count();

		return $result;
	}

	public static function oldgetCountUsers($data = array()) {
		$query = DB::table('users')->join('user_roles', 'users.role_id', '=', 'user_roles.id', 'LEFT')
								   ->join('settings', 'users.brand', '=', 'settings.id', 'LEFT')
								   ->where('users.id', '!=', 1)
								   ->where(function($query_sub)
							            {
							                $query_sub->where('users.deleted_at', '=', '0000-00-00 00:00:00')
								   				  	  ->orWhere('users.deleted_at', '=', NULL);
							            });

		if( CommonHelper::hasValue($data['filter_username']) ) $query->where('username', 'LIKE', '%'.$data['filter_username'].'%');
		if( CommonHelper::hasValue($data['filter_barcode']) ) $query->where('barcode', 'LIKE', '%'.$data['filter_barcode'].'%');
		if( CommonHelper::hasValue($data['filter_user_role']) ) $query->where('user_roles.role_name', 'LIKE', '%'.$data['filter_user_role'].'%');

		return $query->count();
	}

	public static function getBarcodeUser($code) {
		// $query = User::where(DB::raw('BINARY `barcode`'), $code)
		$query = User::where('barcode', '=', $code)
			->where('deleted_at', '=', '0000-00-00 00:00:00')
			->where('role_id', '=', 3);
		DebugHelper::log(__METHOD__);

		if ($query) {
			return $query->first();
		} else {
			throw new Exception( 'Invalid user!');
		}
	}

	public static function getUser($data = array()) {
		$query = User::where('username', '=', $data['username'])
				->where('password', '=', Hash::make($data['password']));

		return $query->first();
	}

	public static function getUserOptions() {
		$query = DB::table('users')->where(function($query_sub)
							            {
							                $query_sub->where('deleted_at', '=', '0000-00-00 00:00:00')
								   				  	  ->orWhere('deleted_at', '=', NULL);
							            })
								   ->orderBy('role_id', 'ASC');

		$result = $query->get();

		return $result;
	}

	public static function getStockPilerOptions() {
		$query = DB::table('users')->where('role_id', '=', '3')
							  	   ->where(function($query_sub)
							            {
							                $query_sub->where('deleted_at', '=', '0000-00-00 00:00:00')
								   				  	  ->orWhere('deleted_at', '=', NULL);
							            })
								   ->orderBy('firstname', 'ASC');

		$result = $query->get();

		return $result;
	}

	public static function getUsersFullname($data = array()) {
		// return User::whereIn('id', $data)->get(array('firstname', 'lastname'))->toArray();
		return User::select(DB::raw('CONCAT(wms_users.firstname, \' \', wms_users.lastname) AS name'))
			->whereIn('id', $data)
			->get()->toArray();
	}
}