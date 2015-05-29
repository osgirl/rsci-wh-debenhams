<?php
/**
* Functions use in DB2/odbc
*
* @package 		SSI-WMS
* @subpackage 	DB2
* @category    	Helpers
* @author 		Dean Francis Casili | fcasili2stratpoint.com | dean.casili@gmail.com
* @version 		Version 1.0
*
*/
class DB2Helper {

    /** @var \dsn  */
    protected $dsn;

    /** @var \username  */
    protected $username;

    /** @var \password  */
    protected $password;

    /** @var \connection instance  */
    protected $db2;

    public function __construct()
    {
        // echo "Connecting to DB2... \n";
        $this->dsn = Config::get('app.db2_dsn');
        $this->username = Config::get('app.db2_username');
        $this->password = Config::get('app.db2_password');

        $this->db2 = @odbc_connect($this->dsn,$this->username,$this->password,SQL_CUR_USE_DRIVER);
        if (!($this->db2)) {
            throw new Exception("Error! Couldn't Connect To DB2 Database. Error Code:  ".odbc_error());
        }

    }

    public function updateRecord($sql)
    {
        $result = @odbc_exec($this->db2,$sql);
        if (!($result)) {
            throw new Exception("Error! Couldn't Run Query. Error Code:  ".odbc_error());
        }

        return $result;
    }

    public function get($sql)
    {
        $tempFieldNames = "";
        $result         = @odbc_exec($this->db2,$sql);

        if (!($result)) {
            throw new Exception("Error! Couldn't Run Query. Error Code:  ".odbc_error());
        }

        unset($tempFieldNames);
        $toReturn = "";
        $i        = 0;
        $j        = 0;

        while(odbc_fetch_row($result))
        {
            //Build tempory
            for ($j = 1; $j <= odbc_num_fields($result); $j++) {
                $field_name = odbc_field_name($result, $j);
                $tempFieldNames[$j] = $field_name;
                $ar[$field_name] = odbc_result($result, $field_name);
            }

            $toReturn[$i] = $ar;
            $i++;
        }

        return $toReturn;
    }

    public function close()
    {
        // echo "Closing odbc connection... \n";
        odbc_close($this->db2);
    }

}
