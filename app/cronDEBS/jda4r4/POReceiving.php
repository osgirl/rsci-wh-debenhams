<?php	

require_once 'library/jdatelnet.php';
require_once 'library/eWMS.php';

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
	

	public function DoReceiving($receiver_no, $invoice_no)
	{
		$jda = $this->jda;

	
 // AND po_details.quantity_ordered <> 0
	 
  
	

$jda->write(ENTER, true); $jda->show();  
$jda->write('08', true); $jda->show(); 
$jda->write('02', true); $jda->show(); 
$jda->write('02', true); $jda->show(); 
 /*
$jda->write($receiver_no, true); $jda->show();*/
/*$jda->write(TAB, true); $jda->show(); 
$jda->write(TAB, true); $jda->show(); 
$jda->write(ENTER, true); $jda->show();  
   

$jda->write(TAB, true); $jda->show(); 
$jda->write(date('m_d_y'), true); $jda->show();
 

$jda->write(END, true); $jda->show();  

$jda->write('SYS', true); $jda->show();  

$jda->write(TAB, true); $jda->show(); 
 
$jda->write($invoice_no, true); $jda->show();
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
$jda->write(TAB, true); $jda->show();

$jda->write(TAB, true); $jda->show();
$jda->write(TAB, true); $jda->show();

   	$jda->write(F5, true); $jda->show();
   	$jda->write('1', true); $jda->show();

   
   
 
 
   	$jda->write(F7, true); $jda->show();

$jda->write(ENTER, true); $jda->show();  
   	$jda->write(F1, true); $jda->show();
   	
   	$jda->write(F7, true); $jda->show();*/
	}
}