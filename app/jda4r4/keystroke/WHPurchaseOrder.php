<?php	

require_once (__DIR__.'/../library/jdatelnet.php');
require_once(__DIR__.'/../config/config.php');


class WHPurchaseOrder
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
	
	public function DoPurchaseOrder($receiver_no,$transfer, $postat, $getNotInPOQty)
	{
		$jda = $this->jda;$jda = $this->jda;
		$jda->write('08', true); $jda->show();
		$jda->write('02', true); $jda->show();
		$jda->write('02', true); $jda->show();
		$jda->write($receiver_no, true); $jda->show();
		$jda->write(ENTER, true); $jda->show(); 
		$jda->write(TAB, true); $jda->show(); 
		$jda->write('  '.date('mdy'), true); $jda->show();
		$jda->write(TAB, true); $jda->show();
		$jda->write('SYS', true); $jda->show(); 

		$jda->write($transfer, true); $jda->show();

		$jda->write(END, true); $jda->show();
		$jda->write('1', true); $jda->show();
		$jda->write(END, true); $jda->show();
		$jda->write('1', true); $jda->show();
		$jda->write(END, true); $jda->show();
		$jda->write('1', true); $jda->show();
		$jda->write(END, true); $jda->show();
		$jda->write('1', true); $jda->show();
		$jda->write(END, true); $jda->show();
		$jda->write(TAB, true); $jda->show();
		$jda->write(TAB, true); $jda->show();
		$jda->write(TAB, true); $jda->show();
		$jda->write('RZ000001', true); $jda->show();
		$jda->write(TAB, true); $jda->show();
		$jda->write('SYS', true); $jda->show();
		$jda->write(F5, true); $jda->show();
		$jda->write('1', true); $jda->show();
		foreach ($getNotInPOQty as $NIP) 
		{
			$jda->write(F10, true); $jda->show();
			$jda->write($NIP['sku'], true); $jda->show();
			$jda->write(TAB, true); $jda->show();
			$jda->write($NIP['quantity_delivered'], true); $jda->show();
			$jda->write(TAB, true); $jda->show();
			$jda->write('RZ000001', true); $jda->show();
			$jda->write(ENTER, true); $jda->show();
			$jda->write(F9, true); $jda->show();
			$jda->write(F1, true); $jda->show();
		}
		$jda->write(F7, true); $jda->show();
		$jda->write(ENTER, true); $jda->show();
		if($postat == '5') 
			{
				$jda->write('Y', true); $jda->show();
			}
		else
			{
				$jda->write('N', true); $jda->show();
			}
		$jda->write(F7, true); $jda->show();
		$jda->write(ENTER, true); $jda->show();
		$jda->write(F1, true); $jda->show();
		$jda->write(F7, true); $jda->show();


	}
}