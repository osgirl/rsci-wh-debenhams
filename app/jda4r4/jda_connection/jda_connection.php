<?php
require_once(__DIR__.'/../config/config.php');

class JDAConnect {
    
    private $system;
    private $lib_name;
    private $username;
    private $password;
    
    public function __construct()
    {
        //instantiate the jda db
        $config = jda_credentials();        
        $this->system   = $config['system'];
        $this->lib_name = $config['lib_name'];
        $this->username = $config['username'];
        $this->password = $config['password'];
    }

    public function getLibrary()
    {
        return $this->lib_name;
    }

    public function connect()
    {
        try {

            $cnString = "odbc:DRIVER={iSeries Access ODBC Driver}; ".
                "SYSTEM={$this->system}; ".
                "DATABASE={$this->lib_name}; ".
                "UID={$this->username}; ".
                "PWD={$this->password};";


            echo "Connecting to JDA... \n";
            echo "SYSTEM : {$this->system}\t";
            echo "LIBRARY : {$this->lib_name}\n";
            echo "UID : {$this->username}\t";
            echo "PWD : ".str_repeat('*', strlen($this->password) + rand(1,20)). "\n";

            $this->dbh = new PDO($cnString,"","");
            //$this->dbh = new PDO($cn_string,"","");    
           // $this->dbh->setAttribute(PDO::ERRMODE_EXCEPTION, PDO::ATTR_CURSOR);
            echo "\nConnected Successfully! \n ";

        } catch (PDOException $e) {
            echo "\nCan't Connect \n {$e->getMessage()} \n";
            exit();
        }
    }

    public function runDb2Query($query) {
        /**
         * @param $query = statement/query
         **/
        echo "QUERY:".$query;
        $statement = $this->dbh->prepare($query);
        if(!$statement)
        {
            echo "fail. error:\n\n";
            print_r($this->dbh->errorInfo());
            exit();
        }
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function runDb2QueryFirst($query)
    {
        $statement = $this->dbh->prepare($query);
        $statement->execute();
        $result  = $statement->fetch();
        return $result;
    }

    
    
    public function Closed() {
        return $this->dbh = null;
    }

    protected function query($query)
    {
        try
        {
            $prep_result = $statement = $this->dbh->prepare($query);
            if(!$prep_result)
            {
                dd('Something went wrong. Please contact your system administrator and provide a screenshot of this screen',
                    $this->dbh->errorInfo(),
                    'Host:'.$this->db_host.' / Name:'.$this->db_name,
                    'query',$query);
            }
            else
            {
                $statement->execute();  
                $result = $statement->fetchAll();
                return $result;
            }
        }       
        catch(\Exception $e)
        {
            dd($e);
        }
    }

}