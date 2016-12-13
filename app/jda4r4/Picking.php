<?php	

require_once 'library/jdatelnet.php';

class Picking
{
	protected $jda;

	protected $db2 = null;
	protected $db2_host;
	protected $db2_username;
	protected $db2_password;
	protected $db2_database;
	
	public function __construct($hostname, $username, $password, $library, $debug_level = 1)
	{
		$this->jda = new jdatelnet($hostname);
		$this->jda->debugLvl = $debug_level;

		$this->db2_host = $hostname;
		$this->db2_username = $username;
		$this->db2_password = $password;
		$this->db2_database = $library;
	}
	
	public function Initiate()
	{
		$jda = $this->jda;
	}
	
	public function DoPicking()
	{
		$jda = $this->jda;
	}
}