<?php

class ManualMove extends Eloquent {

	protected $table = 'manual_move';


	public static function getDB2Info($fromSlot, $upc) {
		$dsn      = Config::get('app.db2_dsn');
		$username = Config::get('app.db2_username');
		$password = Config::get('app.db2_password');
		$tempFieldNames = [];


		$sql = "SELECT WHSLOT from_slot, INVUPC.INUMBR, INVUPC.IUPC upc, WHHAND, WHCOMM, WHHAND - WHCOMM as total
				FROM WHSLSK
				INNER JOIN INVUPC ON WHSLSK.INUMBR = INVUPC.INUMBR
				WHERE WHSLOT = '{$fromSlot}' AND INVUPC.IUPC = {$upc}
				FETCH FIRST 1 ROWS ONLY";
		// WHHAND - WHCOMM
		$connection = odbc_connect($dsn,$username,$password,SQL_CUR_USE_DRIVER) or die("Error! Couldn't Connect To Database. Error Code:  ".odbc_error());

		$res = odbc_exec($connection,$sql) or die("Error! Couldn't Run Query. Error Code:  ".odbc_error());
        unset($tempFieldNames);

		$toReturn = "";
		$i        = 0;
		$j        = 0;

        while(odbc_fetch_row($res))
        {
            //Build tempory
            for ($j = 1; $j <= odbc_num_fields($res); $j++) {
                $field_name = odbc_field_name($res, $j);
                $tempFieldNames[$j] = $field_name;
                $ar[$field_name] = odbc_result($res, $field_name);
    	   	}

            $toReturn[$i] = $ar;
            $i++;
        }

        //result : Array ( [0] => Array ( [WHSLOT] => ZR000001 [INUMBR] => 11270549 [WHHAND] => 1011.00 [WHCOMM] => 0 ) )
		return $toReturn[0];
	}
}