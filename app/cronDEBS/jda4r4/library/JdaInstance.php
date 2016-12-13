<?php namespace App\Library;

use Config, Carbon\Carbon;

class JdaInstance
{
	/*
	|--------------------------------------------------------------------------
	| JdaInstance
	|--------------------------------------------------------------------------
	|
	| Author: Randolf Arevalo
	| Created At: 12/2015
	| Last Update: 2/15/2016
	| Description: Used for connecting to a specified JDA Instance database
	|
	*/

	protected $db_host;
	protected $db_name;
	protected $db_username;
	protected $db_password;

	protected $driver;
	protected $dbh;

	public function __construct()
	{
		try
		{
			$this->driver = Config::get('jda.driver');
			$this->db_host = Config::get('jda.host');
			$this->db_name = Config::get('jda.library');
			$this->db_username = Config::get('jda.username');
			$this->db_password = Config::get('jda.password');

			$cn_string = "odbc:DRIVER={$this->driver}; ".
								"SYSTEM={$this->db_host}; ".
								"DATABASE={$this->db_name}; ".
								"UID={$this->db_username}; ".
								"PWD={$this->db_password};";		
			$this->dbh = new \PDO($cn_string,"","");	
		}
		catch(\Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * Execute the query to the dbh var
	 */
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