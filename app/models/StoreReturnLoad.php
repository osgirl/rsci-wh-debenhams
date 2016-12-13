<?php

class StoreReturnLoad extends Eloquent {

    protected $table = 'store_return_load';
	protected $guarded = array();
	/**
     * The database table used by the model.
     *
     * @var string
     */
   
    /*****For cms*****/
  
    public static function getLoadList($tlnumber)
    {
        $query=db::select(DB::rAw("SELECT *   "));

        return $query;
    }
    public static function getLoadNumberStocktransfer($data = array(), $getCount = false)
    {
        $query = DB::table('store_return_load');
     

        CommonHelper::filternator($query,$data,2,$getCount);
        if( CommonHelper::hasValue($data['filter_entry_date']) ) $query->where('created_at', 'LIKE', '%'.$data['filter_entry_date'].'%');
        $result = $query->get();
        if($getCount) {
            $result = count($result);
        }
        return $result;

    }
    public static function assignToStockPiler($Box_code = '', $data = array())
    {
        $query = load::where('load_code', '=', $Box_code)->update($data);
        DebugHelper::log(__METHOD__, $query);
        
    }
}