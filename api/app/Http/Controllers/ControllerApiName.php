<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use App\Http\Requests;
use App\ModelApiName;
class ControllerApiName extends Controller
{
	protected static $allowed_roles = array(3,4);


//////////////////////////////////////////////////////////////////////
/* function __construct() {
        $this->middleware('oauth', ['except' =>['validateUser']]);

    }*/
    public function postLogin() {

		try {
			$credential = array();
			$credential['username'] = Request::get('username');
			$credential['password'] = Request::get('password');


	if(! CommonHelper::hasValue($credential['username']) ) throw new Exception( 'Username cannot be null!');
	if(! CommonHelper::hasValue($credential['password']) ) throw new Exception( 'Password cannot be null!');

			if(Auth::attempt($credential))
			{
				$deleted_at = Auth::user()->deleted_at;

				if(!in_array(Auth::user()->role_id, self::$allowed_roles)) {
					throw new Exception( 'Account not allowed.');
				}
				//validate if user account has been deleted
				if( $deleted_at > '0000-00-00 00:00:00' ) throw new Exception( 'Invalid username or password!');
				$brandName = Brands::getBrandNameById(Auth::user()->brand_id);

				$user_detail = array(
								'user_id' 	=> Auth::user()->id,
								'username' 	=> Auth::user()->username,
								'firstname'	=>Auth::user()->firstname,
								'lastname'	=> Auth::user()->lastname,
								'role_id'	=> Auth::user()->role_id,
								'brand'  => $brandName[Auth::user()->brand_id]
							);

				if(Auth::user()->role_id == 4) $user_detail['store_code'] = Auth::user()->store_code;

				DebugHelper::log(__METHOD__ .' User detail ',$user_detail);
				return Response::json(array(
					"error" => false,
					"result" => array("user" => $user_detail),
					"message" => 'Successfully logged in!'),
					200
				);
			}
			
			else
			{
				throw new Exception( 'Invalid username or password!');
			}

		}catch(Exception $e) {
			Log::error(__METHOD__ .$e->getMessage());
			return Response::json(array(
				"error" => true,
				"message" => $e->getMessage()),
				400
			);
		}
	}
	 

     public function RPolist($piler_id)
    {
    	try {
			$polist = ModelApiName::GetApiRPoList($piler_id);

			return Response::json(array('result' => $polist),200);
			//return $polist;
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

    }

     public function getSlotCodeList()
    {
    	try {
			$polist = ModelApiName::GetApiSlotCodes();

			return Response::json(array('result' => $polist),200);
			//return $polist;
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

    }
    public function getquery($query)
    {
    	try {
			$polistdetail = ModelApiName::getAPIquery($query);

			return Response::json(array('result' => $polistdetail),200);
			//return $polist;
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

    }
     public function RPolistDetail($receiver_no,$division_id)
    {
    	try {
			$polistdetail = ModelApiName::GetApiRPoListDetail($receiver_no,$division_id);

			return Response::json(array('result' => $polistdetail),200);
			//return $polist;
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

    }

    public function UpdateApiRPoSlot($receiver_no,$division_id)
    {
    	try {
			$poupdatastatus = ModelApiName::UpdateApiRPoSlot($receiver_no,$division_id);

			return Response::json(array('result'),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

    }

    
	public function UserLogin($username,$password)
    {
    	try {
			$user = ModelApiName::UserLogin($username,$password);
			
			return Response::json(array('result' => $user),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

    }
    
	public function UserLoginVerify($username,$password)
    {
    	try {
			$user = ModelApiName::getAPIUserLoginVerify($username,$password);
			
			return Response::json(array('result' => $user),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

    }


    public function UpdateRPoListDetail($receiver_no,$division_id,$upc,$rqty, $slot)
    {
    	try {
			$poList = ModelApiName::UpdateRPoListDetail($receiver_no,$division_id,$upc,$rqty, $slot);
			ModelApiName::UpdateApiRpoList($receiver_no,$division_id);
			return Response::json(array('result' => $poList),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }


    public function RPoListDetailAdd($receiver_no,$division_id,$sku,$upc,$rqty,$userid,$slot,$division_name)
    {
    	try {
			$poList = ModelApiName::RPoListDetailAdd($receiver_no,$division_id,$sku,$upc,$rqty,$userid, $slot,$division_name);

			return Response::json(array('result' => $poList),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }

    
    public function PTLList($piler_id)
    {
    	try {
			$pickinglist = ModelApiName::GetApiPTLList($piler_id);
//ModelApiName::UpdateTLStatus($pickinglist);
			return Response::json(array('result' => $pickinglist),200);
			
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

    }
//////////////////////////////////////////////////////////////////////



  public function PTLListDetail($moved_doc)
    {
    	try {
			$pickinglistdetails = ModelApiName::GetApiPTLListDetail($moved_doc);

			return Response::json(array('result' => $pickinglistdetails),200);
			//return $polist;
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
}
	public function PTLGetBoxCode($store_id)
    {
    	try {
			$getboxcode = ModelApiName::GetApiPTLGetBoxCode($store_id);

			return Response::json(array('result' => $getboxcode),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }
	public function PSTTLGetBoxCode($store_id)
    {
    	try {
			$getboxcode = ModelApiName::GetApiPSTTLGetBoxCode($store_id);

			return Response::json(array('result' => $getboxcode),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }

  public function PTLGetLastBoxCode($store_id,$move_doc)
    {
    	try {
			$getboxcode = ModelApiName::GetApiPTLGetLastBoxCode($store_id, $move_doc);

			return Response::json(array('result' => $getboxcode),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }
  public function PSTTLGetLastBoxCode($store_id,$move_doc)
    {
    	try {
			$getboxcode = ModelApiName::GetApiPSTTLGetLastBoxCode($store_id, $move_doc);

			return Response::json(array('result' => $getboxcode),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }
    public function PTLGetLastBoxCode1($store_id)
    {
    	try {
			$getboxcode = ModelApiName::GetApiPTLGetLastBoxCode1($store_id);

			return Response::json(array('result' => $getboxcode),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }

  public function AddBoxDetail($picklist_id,$box_code,$moved_qty)
    {
    	try 
    	{
			$BoxDetailInsert = ModelApiName::ApiPTLBoxDetailInsert($picklist_id, $box_code, $moved_qty);

			return Response::json(array('result' => $BoxDetailInsert),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
  public function AddSTBoxDetail($picklist_id,$box_code,$moved_qty)
    {
    	try 
    	{
			$BoxDetailInsert = ModelApiName::ApiSSTPTLBoxDetailInsert($picklist_id, $box_code, $moved_qty);

			return Response::json(array('result' => $BoxDetailInsert),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
  public function PTLNewBoxCode($box_code,$store_id,$move_doc,$number,$total)
    {
    	try 
    	{
			$Newboxcode = ModelApiName::ApiPTLNewBoxCode($box_code,$store_id,$move_doc,$number,$total);

			return Response::json(array('result' => $Newboxcode),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
  public function PSTTLNewBoxCode($box_code,$store_id,$move_doc,$number,$total)
    {
    	try 
    	{
			$Newboxcode = ModelApiName::ApiPSTTLNewBoxCode($box_code,$store_id,$move_doc,$number,$total);

			return Response::json(array('result' => $Newboxcode),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
	public function PTLListDetailUpdate($picking_id,$upc,$rcv_qty)
	{
    	try 
    	{
			$ListDetailUpdate = ModelApiName::ApiPTLListDetailUpdate($picking_id,$upc,$rcv_qty);

			return Response::json(array('result' => $ListDetailUpdate),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }
	public function PSTTLListDetailUpdate($picking_id,$upc,$rcv_qty)
	{
    	try 
    	{
			$ListDetailUpdate = ModelApiName::ApiPSTTLListDetailUpdate($picking_id,$upc,$rcv_qty);

			return Response::json(array('result' => $ListDetailUpdate),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }

	public function PTLListUpdate($moved_doc)
	{
    	try 
    	{
			$ListUpdate = ModelApiName::ApiPTLListUpdate($moved_doc);
			return Response::json(array('result' => $ListUpdate),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }
	public function PSTTLListUpdate($moved_doc)
	{
    	try 
    	{
			$ListUpdate = ModelApiName::ApiPSTTLListUpdate($moved_doc);
			return Response::json(array('result' => $ListUpdate),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }
    public function PTLBoxUpdate($box_code,$store_id,$move_doc,$number,$total)
	{
    	try 
    	{
			$BoxUpdate = ModelApiName::ApiPTLBoxUpdate($move_doc, $box_code);
			if(count($BoxUpdate)== 0 )
			{
				ModelApiName::ApiPTLNewBoxCode($box_code,$store_id,$move_doc,$number,$total);
			}
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }
    public function PSTTLBoxUpdate($box_code,$store_id,$move_doc,$number,$total)
	{
    	try 
    	{
			$BoxUpdate = ModelApiName::ApiPSTTLBoxUpdate($move_doc, $box_code);
			if(count($BoxUpdate)== 0 )
			{
				ModelApiName::ApiPTLNewBoxCode($box_code,$store_id,$move_doc,$number,$total);
			}
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }
    
 public function PTLBoxValidate($box_code)
 {
		try 
    	{
			$BoxValidate = ModelApiName::ApiPTLBoxValidate($box_code);
			return Response::json(array('result' => $BoxValidate),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
    }
 public function LBlist($piler_id)
 {
	try 
	{
		$LBlist = ModelApiName::ApiLBlist($piler_id);
		return Response::json(array('result' => $LBlist),200);
	}
	catch(Exception $e) 
	{
		return Response::json(array(
			"error" => true,
			"result" => $e->getMessage()
			),400
		);
	}
}
 public function STLBlist($piler_id)
 {
	try 
	{
		$LBlist = ModelApiName::ApiSTLBlist($piler_id);
		return Response::json(array('result' => $LBlist),200);
	}
	catch(Exception $e) 
	{
		return Response::json(array(
			"error" => true,
			"result" => $e->getMessage()
			),400
		);
	}
}
 public function NewLoadingList($piler_id)
 {
	try 
	{
		$LBlist = ModelApiName::ApigetLoadinglist($piler_id);
		return Response::json(array('result' => $LBlist),200);
	}
	catch(Exception $e) 
	{
		return Response::json(array(
			"error" => true,
			"result" => $e->getMessage()
			),400
		);
	}
}
 public function NewLoadingSTList($piler_id)
 {
	try 
	{
		$LBlist = ModelApiName::ApigetLoadingSTlist($piler_id);
		return Response::json(array('result' => $LBlist),200);
	}
	catch(Exception $e) 
	{
		return Response::json(array(
			"error" => true,
			"result" => $e->getMessage()
			),400
		);
	}
}
 public function NewLoadingListDetails($load_code)
 {
	try 
	{
		$LBlist = ModelApiName::ApigetLoadinglistDetails($load_code);
		return Response::json(array('result' => $LBlist),200);
	}
	catch(Exception $e) 
	{
		return Response::json(array(
			"error" => true,
			"result" => $e->getMessage()
			),400
		);
	}
}
 public function NewLoadingSTListDetails($load_code)
 {
	try 
	{
		$LBlist = ModelApiName::ApigetLoadingSTlistDetails($load_code);
		return Response::json(array('result' => $LBlist),200);
	}
	catch(Exception $e) 
	{
		return Response::json(array(
			"error" => true,
			"result" => $e->getMessage()
			),400
		);
	}
}
 public function UpdateNewLoadingBoxStatus($load_code, $box_code, $status)
 {
	try 
	{
		$LBlist = ModelApiName::ApigetUpdateNewLoadingBoxStatus($load_code, $box_code, $status);
		return Response::json(array('result' => $LBlist),200);
	}
	catch(Exception $e) 
	{
		return Response::json(array(
			"error" => true,
			"result" => $e->getMessage()
			),400
		);
	}
}
 public function UpdateNewLoadingSTBoxStatus($load_code, $box_code, $status)
 {
	try 
	{
		$LBlist = ModelApiName::ApigetUpdateNewLoadingSTBoxStatus($load_code, $box_code, $status);
		return Response::json(array('result' => $LBlist),200);
	}
	catch(Exception $e) 
	{
		return Response::json(array(
			"error" => true,
			"result" => $e->getMessage()
			),400
		);
	}
}
  	public function LBListdetails($load_code)
	{
		try 
		{
			$asdf2d = ModelApiName::ApiLBlistdetails($load_code);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
  	public function STLBListdetails($load_code)
	{
		try 
		{
			$asdf2d = ModelApiName::ApiSTLBlistdetails($load_code);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
  	public function STLBListdetailsBox($move_doc)
	{
		try 
		{
			$asdf2d = ModelApiName::ApiSTLBlistdetailsBox($move_doc);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
	public function LBListDetailbox($move_doc)
	{
		try 
		{
			$asdf2d = ModelApiName::ApiLBListDetailbox($move_doc);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
	public function UpdateLoadingStatus($load_code,$date)
	{
		try 
		{
			$asdf2d = ModelApiName::ApiUpdateLoadingStatus($load_code,$date);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
	public function UpdateLoadingSTStatus($load_code,$date)
	{
		try 
		{
			$asdf2d = ModelApiName::ApiUpdateLoadingSTStatus($load_code,$date);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
	public function PStoreList($piler_id)
	{

		try 
		{
			$asdf2d = ModelApiName::getApiSOPiler($piler_id);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}




	}
	public function PStoreListDetail($box_code)
	{

		try 
		{
			$asdf2d = ModelApiName::getApiSOboxnumber($box_code);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}




	}
	public function PStoreListDetailUpc($moved_doc, $box_code)
	{

		try 
		{
			$asdf2d = ModelApiName::getAPIPStoreListDetailUpc($moved_doc, $box_code);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}




	}
	public function PStoreListDetailContent($boxcode, $move_doc)
	{

		try 
		{
			$asdf2d = ModelApiName::getApiSOdetailContent($boxcode, $move_doc);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}




	}
	public function UpdateLoadingBoxStatus($move_doc,$boxcode, $status)
	{

		try 
		{
			$asdf2d = ModelApiName::getApiUpdateLoadingBoxStatus($move_doc,$boxcode, $status);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}




	}
	public function UpdateSTLoadingBoxStatus($move_doc,$boxcode, $status)
	{

		try 
		{
			$asdf2d = ModelApiName::getApiUpdateSTLoadingBoxStatus($move_doc,$boxcode, $status);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}




	}
	public function UpdateStoreOrderUpc($move_doc, $upc, $rcv_qty)
	{

		try 
		{
			$asdf2d = ModelApiName::getApiUpdateStoreOrderUpc($move_doc, $upc, $rcv_qty);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}


	}
    public function UpdateStoreOrderBox($move_doc, $box_code, $upc, $rcv_qty)
	{

		try 
		{
			$asdf2d = ModelApiName::getAPIUpdateStoreOrderBox($move_doc, $box_code, $upc, $rcv_qty);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
    public function UpdateStoreOrderStatus($move_doc )
	{

		try 
		{
			$asdf2d = ModelApiName::getAPIUpdateStoreOrderStatus($move_doc );
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
	public function RSTList($piler_id)
	{

		try 
		{
			$asdf2d = ModelApiName::getAPIRSTList($piler_id);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
	public function RSTListDetail($mts_no)
	{

		try 
		{
			$asdf2d = ModelApiName::getAPIRSTListDetail($mts_no);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}	
	public function PSTList($piler_id)
	{

		try 
		{
			$asdf2d = ModelApiName::getAPIPSTList($piler_id);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}	
	public function PSTListDetail($mts_no)
	{

		try 
		{
			$asdf2d = ModelApiName::getAPIPSTListDetail($mts_no);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}	
	public function RRLList($piler_id)
	{

		try 
		{
			$asdf2d = ModelApiName::getAPIRRLList($piler_id);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
	public function RSTListDetailUpdate($mts_no, $upc, $rqty)
	{
		
		try 
		{
			$asdf2d = ModelApiName::getAPIRSTListDetailUpdate($mts_no, $upc, $rqty);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
	public function RSTListDetailAdd($mts_no, $upc, $rqty)
	{
		
		try 
		{
			$asdf2d = ModelApiName::getAPIRSTListDetailAdd($mts_no, $upc, $rqty);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
	public function RSTListUpdateStatus($mts_no)
	{
		try 
		{
			$asdf2d = ModelApiName::getAPIRSTListUpdateStatus($mts_no);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}

	}
	public function RRLListDetailUpdate($mts_no, $upc, $rqty)
	{
		try 
		{
			$asdf2d = ModelApiName::getAPIRRLListDetailUpdate($mts_no, $upc, $rqty);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
	public function RRLListDetail($mts_no)
	{
		try 
		{
			$asdf2d = ModelApiName::getAPIRRLListDetail($mts_no);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
	public function RRLListDetailAdd($mts_no, $upc, $qty)
	{
		try 
		{
			$asdf2d = ModelApiName::getAPIRRLListDetailAdd($mts_no, $upc, $qty);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
	public function RRLListUpdateStatus($mts_no)
	{
		try 
		{
			$asdf2d = ModelApiName::getAPIRRLListUpdateStatus($mts_no);
			return Response::json(array('result' => $asdf2d),200);
		}
		catch(Exception $e) 
		{
			return Response::json(array(
				"error" => true,
				"result" => $e->getMessage()
				),400
			);
		}
	}
}