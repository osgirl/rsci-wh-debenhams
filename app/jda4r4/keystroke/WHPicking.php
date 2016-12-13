<?php	

require_once (__DIR__.'/../library/jdatelnet.php');
require_once(__DIR__.'/../config/config.php');


class WHPicking
{
	protected $jda;

	protected $db2 = null;
	protected $db2_host;
	protected $db2_username;
	protected $db2_password;
	protected $db2_database;

	public function __construct($debug_level = 1)
	{
		

        $config = jda_credentials();        
		$this->db2_host = $config['system'];
		$this->db2_username = $config['username'];
		$this->db2_password =$config['password'];
		$this->db2_database = $config['lib_name'];

		$this->jda = new jdatelnet($this->db2_host);
		$this->jda->debugLvl = $debug_level;
	}

	public function Login()
	{		
		$jda = $this->jda;
		$jda->login(	$this->db2_username, 	$this->db2_password);
		$result = $jda->screenCheck('Merchandise Management System');
		if($result)
		{
			return true;
		}
		else
		{
			$result = $jda->screenCheck('is allocated to another job.');
			if($result)
			{
				$jda->write(ENTER, true); $jda->show();
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	public function Initiate()
	{
		$jda = $this->jda;
	}
	
	public function getDoPicking($tl_number)
	{
		$jda = $this->jda;
		$jda->write('09', true); $jda->show();
		$jda->write('01', true); $jda->show();
		$jda->write('10', true); $jda->show();
		$jda->write('18', true); $jda->show();
		$jda->write('8001', true); $jda->show();
		$jda->write(TAB, true); $jda->show();
		$jda->write($tl_number, true); $jda->show();
		$jda->write(TAB, true); $jda->show();
		$jda->write('SYS', true); $jda->show();
		$jda->write(F6, true); $jda->show();
		$jda->write(F7, true); $jda->show(); 
/*
	 	while ( screenCheck('is allocated to another job.')) 
	 	{ 
			$jda->write(F7, true); $jda->show();
	 	} */

		$jda->write(F7, true); $jda->show();
		$jda->write(F10, true); $jda->show();
		$jda->write(ENTER, true); $jda->show();
		$jda->write(F7, true); $jda->show();
	}
}