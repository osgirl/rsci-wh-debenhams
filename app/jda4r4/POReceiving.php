<?php	

require_once 'library/jdatelnet.php';

class POReceiving
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

	public function Login($username, $password, $library)
	{		
		$jda = $this->jda;
		$jda->login($username, $password);
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
	
	public function DoReceiving($po_number, $invoice_number)
	{
		$jda = $this->jda;
	 
		$jda->write('08', true); $jda->show();
		$jda->write('02', true); $jda->show();

		$jda->write('02', true); $jda->show();
		$jda->write(TAB, true); $jda->show();
		$jda->write($po_number, true); $jda->show();
  
	  $jda->write(TAB, true); $jda->show(); 
	  $jda->write(ENTER, true); $jda->show();
  $jda->write(TAB, true); $jda->show();
	 	$jda->write('062216', true); $jda->show();
 		$jda->write(END, true); $jda->show();
 	  	$jda->write(TAB, true); $jda->show();
 	  
 	  	$jda->write('SYS', true); $jda->show();
  
 	  $jda->write($invoice_number, true); $jda->show();
 	 $jda->write(TAB, true); $jda->show();
 
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

 
  	 	 $jda->write(F11, true); $jda->show();
  	 	 $jda->write('1', true); $jda->show();
  	 	 $jda->write('10', true); $jda->show();
  /*	 	$jda->write(F7, true); $jda->show();
		$jda->write(ENTER, true); $jda->show(); */
 
   

 
	}
}