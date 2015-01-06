<?php

class LetdownDetails extends Eloquent {

	protected $table = 'letdown_details';

	/**
	* Get letdown list based on details
	*
	* @example  LetdownDetails::getLetDownLists()
	*
	* @return array of upc
	*/
	public static function getLetDownLists()
	{
		$letdownList = LetdownDetails::select("letdown_details.sku as sku","department.description as sub_dept","product_lists.description",  "letdown.created_at")
			->join('letdown', 'letdown.move_doc_number', '=', 'letdown_details.move_doc_number')
			->leftJoin('product_lists' , 'product_lists.upc', '=', 'letdown_details.sku')
			->leftJoin('department', 'department.sub_dept', '=', 'product_lists.sub_dept')
			->where('move_to_picking_area', '=', Config::get('letdown_statuses.unmoved'))
			->groupBy('sku')
			->get();

		return $letdownList;

	}

	/**
	* Get Letdown detail by SKU/UPC
	*
	* @example  LetdownDetails::getLetDownDetailBySKU({sku})
	*
	* @param  sku      sku/upc of product
	* @return array of letdown details by slot
	*/
	public static function getLetDownDetailBySKU($sku, $docNos)
	{
		$letdownDetails = LetdownDetails::select(DB::raw("sum(wms_letdown_details.quantity_to_letdown) as quantity_to_letdown, sum(wms_letdown_details.moved_qty) as moved_qty, wms_letdown_details.from_slot_code"))
			->whereIn('move_doc_number', $docNos)
			->where('move_to_picking_area', '=', Config::get('letdown_statuses.unmoved'))
			->where('sku', '=', $sku)
			->groupBy('from_slot_code')
			->get();

		return $letdownDetails;
	}

	/**
	* Get Letdown detail by move_doc_number
	*
	* @example  LetdownDetails::getLetDownDetailByDocNo({$docNo})
	*
	* @param  sku      move_doc_nmber
	* @return array of letdown details by move document number
	*/
	public static function getLetDownDetailByDocNo($docNo)
	{
		$letdownDetails = LetdownDetails::select('letdown_details.sku',"product_lists.description", 'store_code', 'move_doc_number', 'from_slot_code', 'quantity_to_letdown', 'moved_qty')
			->leftJoin('product_lists' , 'product_lists.upc', '=', 'letdown_details.sku')
			->where('move_doc_number', '=', $docNo)
			->get();

		return $letdownDetails;
	}

	/**
	* Gets the document numbers that are assigned on the stock piler
	*
	* @example  LetdownDetails::getLetdownDocumentNumbers({sku}, {id})
	*
	* @param  $sku      sku/upc of product
	* @param  $id       stock piler id
	* @return array of document numbers
	*/
	public static function getLetdownDocumentNumbers($sku, $id)
	{
		$documentNumbers = LetdownDetails::where('move_to_picking_area', '=', Config::get('letdown_statuses.unmoved'))
			->whereIn('assigned_user_id', array(0, $id))
			->where('sku', '=', $sku)
			->groupBy('move_doc_number')
			->lists('move_doc_number');
		return $documentNumbers;
	}

	/**
	* Check if the the letdown document is assigned
	*
	* @example LetdownDetails::checkAndAssign({docNos}, {sku}, {id})
	*
	* @param  docNos 	array   document numbers
	* @param  sku   	int		sku/upc of the product
	* @param  id   		int     stock piler id
	* @return Status
	*/
	public static function checkAndAssign($docNos, $sku, $id)
	{
        $lockTag = time();
        $letdownAssigned = self::getSumOfAssigned($docNos, $sku);
		if($letdownAssigned > 0 ) {
			$currentlyAssigned = self::getCurrentlyAssigned($docNos, $sku, $id);
			//assigns all with 0 assigned
			if(in_array(0, $currentlyAssigned)) {
				self::assignLetdown(array_keys($currentlyAssigned), $sku, $id, $lockTag);
				return array_keys($currentlyAssigned);
			}
			//returns all document numbers where the current user is assigned
			if(in_array($id, $currentlyAssigned)) {
				return array_keys($currentlyAssigned);
			}
			throw new Exception("This upc has already been assigned");
		} else {
			self::assignLetdown($docNos, $sku, $id, $lockTag);
		}
		return $docNos;
	}

	/**
	* Check if the the letdown document is assigned
	*
	* @example LetdownDetails::checkAndAssignv2({docNos}, {id})
	*
	* @param  docNos 	array   document numbers
	* @param  id   		int     stock piler id
	* @return Status
	*/
	public static function checkAndAssignv2($docNo, $id)
	{
        $lockTag = time();

        // check if doc_no has already an assigned user_id
        // if none assign the letdown doc_no to the first user_id
        // else prohibit other user to access that letdown

        $user = LetdownDetails::select('assigned_user_id')
        	->where('letdown_details.move_doc_number', '=', $docNo)
        	->join('letdown', 'letdown.move_doc_number', '=', 'letdown_details.move_doc_number')
        	->groupBy('letdown_details.move_doc_number')
        	->first()->toArray();

        if ( $user['assigned_user_id'] == 0 ) {
        	LetdownDetails::where('move_doc_number', '=', $docNo)
        		->update(array('assigned_user_id' => $id, 'lock_tag' => $lockTag,));
        } else {

        	if ( $user['assigned_user_id'] != $id ) {
        		throw new Exception("This letdown has already been assigned to a different stock piler");

        		return false;
        	}
        }

        return true;
	}

	/**
	* Gets sum of assigned ids, used for check if the group of details has someone assigned to it
	*
	* @example  self::getSumOfAssigned();
	*
	* @param  $docNos   Document number
	* @param  $sku  	Sku or upc
	* @return sum of assigned user id
	*/
	private static function getSumOfAssigned($docNos, $sku)
	{
		$result = LetdownDetails::select(DB::raw("sum(wms_letdown_details.assigned_user_id) as assigned_user_id"))
        	->whereIn('move_doc_number', $docNos)
			->where('sku', '=', $sku)
			->pluck('assigned_user_id');
		return $result;
	}

	/**
	* Gets details where the current user has already been assigned
	*
	* @example  self::getCurrentlyAssigned();
	*
	* @param  $docNos   Document number
	* @param  $sku  	Sku or upc
	* @param  $id       Stock piler id
	* @return array of assigned user id and document number
	*/
	private static function getCurrentlyAssigned($docNos, $sku, $id)
	{
		$result  = LetdownDetails::whereIn('move_doc_number', $docNos)
				->where('sku', '=', $sku)
				->whereIn('assigned_user_id', array(0, $id))
				->lists('assigned_user_id', 'move_doc_number');
		return $result;
	}


	/**
	* Assign to stockpiler
	*
	* @example  self::assignLetdown();
	*
	* @param  $docNos   Document number
	* @param  $sku  	Sku or upc
	* @param  $id       Stock piler id
	* @param  $lockTag  Lock tag
	* @return void
	*/
	private static function assignLetdown($docNos, $sku, $id, $lockTag)
	{
		LetdownDetails::whereIn('move_doc_number', $docNos)
			->where('sku', '=', $sku)
			->where('assigned_user_id', '=', 0)
            ->update(array(
            	'lock_tag'		   =>   $lockTag,
            	'assigned_user_id' =>   $id,
            	'updated_at'	   =>	date('Y-m-d H:i:s')));

        return true;
	}


	/**
	* Changed moved_qty of a letdown list detail
	*
	*/
	public static function moveToPicking($letdownDetailId, $movedQty)
	{
		//static to slot, PCK00001,
		LetdownDetails::where('id' , '=', $letdownDetailId)
			->update(array(
				'updated_at'			=>	date('Y-m-d H:i:s'),
				'moved_qty'				=> 	$movedQty,
				'move_to_picking_area'	=>	Config::get('letdown_statuses.moved'),
				'to_slot_code'			=>  'PCK00001'
				));
		return true;
	}

	/*
	*
	*  Checks if letdown detail has already been moved
	*
	*/
	public static function getLetdownDetail($docNos, $sku, $slot, $id)
	{
		$letdownDetail = LetdownDetails::whereIn('move_doc_number' , $docNos)
			->where('sku' , '=', $sku)
			->where('move_to_picking_area','=', Config::get('letdown_statuses.unmoved'))
			->where('assigned_user_id', '=', $id)
			->where('from_slot_code' , '=', $slot)
			->get();
		if(count($letdownDetail) === 0 ) {
			throw new Exception("The letdown document does not contain the passed SKU or the sku does not exist in the given slot.");
		}
		return $letdownDetail;
	}


	/******************Methods for CMS only*************************/

	public static function getLetdownDetails($move_doc_number,$data = array(), $getCount = false)
	{

		$query = DB::table('letdown_details')
					->join('stores', 'stores.store_code', '=', 'letdown_details.store_code', 'LEFT')
					->join('product_lists', 'letdown_details.sku', '=', 'product_lists.upc')
					->where('letdown_details.move_doc_number', '=', $move_doc_number);

		if( CommonHelper::hasValue($data['filter_sku']) ) $query->where('letdown_details.sku', 'LIKE', '%'.$data['filter_sku'].'%');
		if( CommonHelper::hasValue($data['filter_store'])) $query->where('stores.store_name', 'LIKE', '%'.$data['filter_store'].'%');
		if( CommonHelper::hasValue($data['filter_slot']) ) $query->where('letdown_details.from_slot_code', 'LIKE', '%'. $data['filter_slot'].'%');

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='sku') $data['sort'] = 'letdown_details.sku';
			if ($data['sort']=='slot') $data['sort'] = 'letdown_details.from_slot_code';
			if ($data['sort']=='store') $data['sort'] = 'stores.store_name';


			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page'])  && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}
		$result = $query->get();

		if($getCount) {
			$result = $query->count();
		}



		return $result;
	}

	public static function getLockTags($data = array(), $getCount=false)
	{
		$query = LetdownDetails::select(DB::raw('wms_letdown_details.*, wms_users.*, sum(move_to_picking_area) as sum_moved'))
			->join('users', 'users.id', '=', 'letdown_details.assigned_user_id');

		if( CommonHelper::hasValue($data['filter_stock_piler']) ) $query->where('users.id', '=', $data['filter_stock_piler']);
		if( CommonHelper::hasValue($data['filter_doc_no'])) $query->where('letdown_details.move_doc_number', 'LIKE', '%'.$data['filter_doc_no'].'%');
		if( CommonHelper::hasValue($data['filter_sku']) ) $query->where('letdown_details.sku', 'LIKE', '%'. $data['filter_sku'].'%');

		if( CommonHelper::hasValue($data['sort']) && CommonHelper::hasValue($data['order']))  {
			if ($data['sort']=='lock_tag') $data['sort'] = 'letdown_details.lock_tag';

			$query->orderBy($data['sort'], $data['order']);
		}

		if( CommonHelper::hasValue($data['limit']) && CommonHelper::hasValue($data['page'])  && !$getCount)  {
			$query->skip($data['limit'] * ($data['page'] - 1))
		          ->take($data['limit']);
		}

		if($getCount) {
			$result = $query->count(DB::raw('distinct lock_tag'));

		} else {
			$query->groupBy('letdown_details.lock_tag');
			$result = $query->get();
		}

		return $result;
	}

	public static function getLockTagDetails($lockTag)
	{
		$lockTagDetails = LetdownDetails::where('lock_tag', '=', $lockTag)
			->join('stores', 'stores.store_code', '=', 'letdown_details.store_code')
			->join('product_lists','product_lists.upc', '=', 'letdown_details.sku' )
			->get();
		$sumMoved =  LetdownDetails::select(DB::raw('sum(move_to_picking_area) as sum_moved'))
			->where('lock_tag', '=', $lockTag)
			->pluck('sum_moved');

		$result = array('details'	=> $lockTagDetails,
				'sum_moved'	=> $sumMoved);
		return $result;
	}



	public static function unlockTag($lockTags)
	{
		LetdownDetails::whereIn('lock_tag', $lockTags)
			->where('move_to_picking_area' , '!=', Config::get('letdown_statuses.moved'))
			->update(array('assigned_user_id'=> 0,
					'lock_tag'		=> 0,
					'updated_at'	=>	date('Y-m-d H:i:s')));
		return true;
	}

	public static function saveDetail($docNo, $data, $user_id)
	{
		//UPDATE ssi.wms_letdown_details SET moved_qty = 250, to_slot_code = 'PCK00001', move_to_picking_area = 1
		// WHERE from_slot_code = 'CRAC' AND move_doc_number = 8858 AND sku = '2800090900154'
		foreach ($data as $key => $value) {
			foreach ($value as $v) {
				LetdownDetails::where('from_slot_code', '=', $v['from_slot_code'])
					->where('move_doc_number', '=', $docNo)
					->where('sku', '=', $v['sku'])
					->update(array(
							'moved_qty'            => $v['moved_qty'],
							'to_slot_code'         => 'PCK00001',
							'move_to_picking_area' => Config::get('letdown_statuses.moved'),
							'updated_at'           =>	date('Y-m-d H:i:s')
						)
					);
			}
		}

		return true;
	}

}