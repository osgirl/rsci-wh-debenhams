<?php


class Sqlconnect {
    
    
    function __construct($system, $lib_name, $username, $password)
    {
        $this->system   = $system;
        $this->database = $lib_name;
        $this->username = $username;
        $this->password = $password;
    }
    
    public function Connect() {
        try {
            $cnString = "mysql:host={$this->system};dbname={$this->database}; --local-infile";

            echo "Connecting to MYSQL(RMCI)... \n";
            echo "HOST: {$this->system}\t";
            echo "DBNAME: {$this->database}\n";
            echo "USERNAME: {$this->username}\t";
            echo "Password: ".str_repeat('*', strlen($this->password) + rand(1,20)). "\n";

            $this->dbh = new PDO($cnString, $this->username, $this->password,array(
                PDO::MYSQL_ATTR_LOCAL_INFILE => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true
            ));

            echo "\nConnected Successfully! \n";
        } catch (PDOException $e) {
            echo "\nCan't Connect \n {$e->getMessage()} \n";
            exit(str_repeat('-', 100)."\n");
        }
    }

    public function runQuery($query) {
        /**
         * @param $query = statement/query
         **/
      $statement = $this->dbh->prepare($query);
      $statement->execute();
      $result  = $statement->fetchAll(PDO::FETCH_ASSOC);
      return $result;
    }

    public function updateQuery($query)
    {
        /**
         * @param $query = statement/query
         **/
        $statement = $this->dbh->prepare($query);
        $statement->execute();
    }

    public function insertQuery($query)
    {
        return $this->dbh->exec($query);
    }


}