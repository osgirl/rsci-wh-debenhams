<?php
include_once('config/config.php');

class odbcConnection{
	var $jdaLib; // = 'MMFMTLIB';
   	var $user; // = 'STRATPGMR';  //Username for the database
   	var $pass; // = 'PASSWORD'; //Password
   	var $connHandle; //Connection handle
   	var $tempFieldNames; //Tempory array used to store the fieldnames, makes parsing returned data easier.
   	var $dateToday;

   	public function __construct()
    {
        $creds = jda_credentials();
        $this->jdaLib = $creds['jda_lib'];
        $this->user = $creds['user'];
        echo "$this->user \n";
        $this->pass = $creds['password'];
        echo "$this->pass \n";
		$this->connectDatabase();
	}

	public function getDate()
    {
		return $this->dateToday = date('ymd'); //format of date in db2: 140509
	}

   	public function connectDatabase()
    {
        echo "Connecting to DB2... \n";
		// $dsn_link = "DRIVER=iSeries Access ODBC Driver;SYSTEM=172.16.1.1;DBQ={$this->jdaLib}";
		$dsn_link = "DRIVER=IBM i Access ODBC Driver 64-bit;SYSTEM=172.16.1.1;DBQ={$this->jdaLib}";
        echo "$dsn_link \n";
        $handle = @odbc_connect($dsn_link,$this->user,$this->pass,SQL_CUR_USE_DRIVER) or die("Error! Couldn't Connect To Database. Error Code:  ".odbc_error());
        $this->connHandle = $handle;
        echo "$this->connHandle \n";
        return true;
    }

    private function runStoredQuery($query, $returns_results){

    	if($returns_results == false){
            return false;
        }

    	$toReturn = "";
        $res = @odbc_exec($this->conn_handle, "exec ".$query."") or die("Error! Couldn't Run Stored Query. Error Code:  ".odbc_error());
        unset($this->tempFieldNames);
        $i = 0;
        $j = 0;

        while(odbc_fetch_row($res))
        {
              //Build tempory
            for ($j = 1; $j <= odbc_num_fields($res); $j++)
            {
                $field_name = odbc_field_name($res, $j);
                $this->tempFieldNames[$j] = $field_name;
                $this->tempFieldNames[$j];
                $ar[$field_name] = odbc_result($res, $field_name);
            }

            $toReturn[$i] = $ar;
            $i++;
        }

 	  return $toReturn;
    }

    public function runSQL($query,$returns_results)
    {
    	echo "Executing SQL: $query \n";
    	$toReturn = "";

        $res = @odbc_exec($this->connHandle,$query) or die("Error! Couldn't Run Query. Error Code:  ".odbc_error());
            unset($this->tempFieldNames);
        if($returns_results == false){
            return false;
        }

        $i = 0;
        $j = 0;

        while(odbc_fetch_row($res))
        {
            //Build tempory
            for ($j = 1; $j <= odbc_num_fields($res); $j++)
               {
                    $field_name = odbc_field_name($res, $j);
                    $this->tempFieldNames[$j] = $field_name;
                    $ar[$field_name] = odbc_result($res, $field_name);
               }

            $toReturn[$i] = $ar;
            $i++;
        }

     	return $toReturn;
    }

    public function getUnique($sql, $field)
    {
        $rs = odbc_exec($this->connHandle, $sql);
        $ar = array();
        // $arr = odbc_fetch_array($rs);
        // var_dump($arr);
        while(odbc_fetch_row($rs))
            {
                $ar[] = odbc_result($rs, $field);
            }
        return $ar;
    }

    public function count($sql)
    {
        $rs = odbc_exec($this->connHandle, $sql);
        $arr = odbc_fetch_array($rs);
        var_dump($arr);
    }

    public function displayResult($data, $key)
    {
    	$rows = count($data);
        $keys = count($key);
        $i = 0;

    	while($i < $rows){
            $j = 1;
            echo "Echoing Row $i:\n";

                while($j < $keys - 1){

                    //$data[row][field];
                    $result = $data[$i][$key[$j]];
                    $field = $key[$j];
                    echo("Field '".$field."' : ".$result." \n");

                    $j++;
                }
            echo "\n----\n\n";
            $i++;
        }
    }

    //TODOS: header column
	public function export($data, $csv_filename, $header_column = array(), $additional_val = array())
    {
		echo "Exporting to CSV \n";

		// $dateFormat 		= date('Ymd').'-'.time();
		$dateFormat         = time();
		$formatted_filename = "{$csv_filename}_{$dateFormat}.csv";
		$fp                 = fopen('db2_dump/' . $formatted_filename, 'wb');
		$header_column      = array_filter($header_column);

        if(!empty($additional_val)) $value = $additional_val; //add header in the csv
        if(!empty($header_column)) fputcsv($fp, $header_column); //add header in the csv
		foreach ($data as $key => $value) {
			// print_r($value);
	  		// $output = array_filter(array_map('trim', $value)); //filter white spaces
            // $output = $value; //filter white spaces
            $output = array_map('trim', $value);
	      	fputcsv($fp, $output);
	  	}
	  	fclose($fp);

        return true;
	}

	public function close()
    {
		echo "Closing odbc connection... \n";
		odbc_close($this->connHandle);
	}
}