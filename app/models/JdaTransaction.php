<?php

class JdaTransaction extends Eloquent {

	protected $guarded = array(); 
	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'transactions_to_jda';

    public static function insert($data)
    {
        $record = JdaTransaction::where('module', '=', $data['module'])
            ->where('jda_action', '=', $data['jda_action'])
            ->where('reference', '=', $data['reference'])
            ->first();
        
        if($record === null ) {
            $result = DB::table('transactions_to_jda')->insert($data);
            return $result;
        } 
        
    	return false;
    }
}