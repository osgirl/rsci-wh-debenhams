<?php //namespace App\Classes\JDABridge;

include_once('jdatelnet.php');

class JDABridge {
	
	public $jda_username;
	public $jda_password;	
	public $jda; //represents the native jda green screen
	public $show_debug;	
	public $last_error_reason;
	public $library;
	
	/**
	 * Initiate the JDA objects
	 * @param string $username jda username
	 * @param string $password jda password
	 * @param string $host the IP address of the JDA server
	 * @param bool $show_debug specify if the output should show the green screen
	 */
	public function __construct($show_debug = false, $host, $library)
	{	
		$this->jda = new jdatelnet($host);
		$this->show_debug = $show_debug;
		$this->library = $library;
	}
	
	public function LastErrorReason()
	{
		return $this->last_error_reason;
	}
	
	/**
	 * login to JDA
	 */
	public function LoginToJDA()
	{		
		$jdax = $this->jda;
		$jdax->login($this->jda_username,$this->jda_password,"MMRSCLIB");		

		$result = $jdax->screen;
		$this->display($result);
		
		if (strpos($result, 'Merchandise Management System') !== false) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * login to JDA with tested scenarios
	 */
	public function LoginToJDA2($username, $password)
	{		
		$jdax = $this->jda;
		$this->jda_username = $username;
		$this->jda_password = $password;
		
		$login_result = $this->ExecuteLogin($this->jda_username, $this->jda_password);		

		$result = $login_result;
		$this->display($result);
		
		if (strpos($result, 'Management System') !== false) 
		{
			return true;
		}
		else
		{
			if($this->IsWordExists($result, 'is allocated to another job.'))
			{
				$jdax->write(ENTER,true); $this->display($jdax->screen);
				return true;
			}
			else
			{
				if($this->IsWordExists($result, 'cannot sign on.'))
				{
					$this->last_error_reason = 'user cannot sign on (profile disabled)';
				}
				
				if($this->IsWordExists($result, 'Next not valid sign-on disables user profile.'))
				{
					$this->last_error_reason = 'next failed login will disable user profile';
				}
				
				return false;
			}
		}
	}
	
	public function ExecuteLogin($username, $password)
	{
		$jdax = $this->jda;		
		$jdax->write($username,true); $this->display($jdax->screen);
		
		if(strlen($username) < 10)
		{
			$jdax->write(TAB,true); $this->display($jdax->screen);					
		}
		
		$jdax->write($password,true); $this->display($jdax->screen);					
		$jdax->write(ENTER,true); $this->display($jdax->screen);
		return $jdax->screen;
	}
	
	/**
	 * Go to the Item Replenishment Screen
	 */
	public function GotoItemReplenishment()
	{
		$jdax = $this->jda;
		$jdax->write("12",true); $this->display($jdax->screen);					
		$jdax->write("02",true); $this->display($jdax->screen);
		$jdax->write("17",true); $this->display($jdax->screen);
	}
	
	public function EndReplenishment()
	{
		$jdax = $this->jda;		
		$jdax->write(F1,true); $this->display($jdax->screen);					
	}
	
	public function LogOff()
	{
		$this->jda->write('F1',true); $this->display($this->jda->screen);
		$this->jda->write('F7',true); $this->display($this->jda->screen);
	}
	
	/**
	 * Enter the SKU information for replenishment
	 * NOTE: GotoItemReplenishment must be called before this.
	 */
	public function ItemReplenishment($sku, $start_date, $replenishment_code, $model_stock, $item_profile = "REPL")
	{
		$jdax = $this->jda;
		
		//go to item replenishment
		$jdax->write(TAB,true);		
		$jdax->write(TAB,true);

		//enter the SKU information. this is where we can enter styles as well
		$jdax->write($sku,true); $this->display($this->jda->screen); 
		$jdax->write(ENTER,true); $this->display($this->jda->screen); 
		
		if($this->IsWordExists($jdax->screen,'The SKU is invalid'))
		{
			$this->last_error_reason = 'The SKU provide is invalid';
			$jdax->write(F1,true); $this->display($jdax->screen);
			$jdax->write("17",true); $this->display($jdax->screen);
			return false;
		}
		else
		{
			$jdax->write(END,true); $this->display($this->jda->screen); 
			$jdax->write(BACKTAB,true); $this->display($jdax->screen);
			
			//INPUT START DATE REPLENISHMENT			
			$jdax->write($start_date,true);
			$jdax->write(TAB.TAB,true);
			//echo 'start date';
			$this->display($jdax->screen); 

			//INPUT REPLENISHMENT CODE
			$jdax->write($replenishment_code,true);
			//echo 'replenishment code';
			$this->display($jdax->screen);
			
			//INPUT MODEL STOCK
			$jdax->write($model_stock,true); 
			//echo 'model stock';
			$jdax->write(TAB.TAB.TAB.TAB.TAB.TAB,true);
			
			//INPUT ITEM PROFILE
			$jdax->write($item_profile,true);
			//echo 'replenishment code';
			$this->display($jdax->screen);
			
			//F7 TO SAVE
			$jdax->write(F7,true);
			//echo 'save';
			$result = $jdax->screen;
			$this->display($result);
						
			$jdax->write(F7,true); $this->display($jdax->screen);
			$jdax->write("17",true); $this->display($jdax->screen);
			
			if($this->IsWordExists($result,'Has been placed on the JOBQ'))
			{			
				return true;
			}
			else
			{
				$this->last_error_reason = 'Error on JDA enrollment. Please inform your system admin. last screen:'."\n".
				$this->displayForLog($result);
				return false;
			}			
		}		
	}
	
	public function GotoMaintainSKU($from_previous)
	{
		$jdax = $this->jda;
		//OPTION 23 - SKU MODEL  STOCK OVERRIDES
		if($from_previous)
		{
			$jdax->write("23",true); $this->display($jdax->screen);
		}
		else
		{
			$jdax->write("12",true); $this->display($jdax->screen);
			$jdax->write("02",true); $this->display($jdax->screen);
			$jdax->write("23",true); $this->display($jdax->screen);
		}
	}
		
	/**
	 * Maintain SKU information
	 */
	public function MaintainSKU($sku, $store_code, $avg_model_stock, $order_at, $maximum_stock, $display_min)
	{
		$jdax = $this->jda;
				
		//INPUT SKU
		$jdax->write($sku,true); $jdax->write(TAB,true); 
		
		$result = $this->display($jdax->screen);
		if($this->IsWordExists($result, 'SKU is invalid'))
		{
			$this->last_error_reason = 'The SKU provide is invalid';
			return false;
		}
		else
		{		
			//TO CLEAR THE STORE
			$jdax->write(END,true); $this->display($jdax->screen); //screen 5 displays the item info

			//INPUT STORE
			$jdax->write($store_code,true); $jdax->write(ENTER,true); 
			$result = $jdax->screen; //006 displays store info
			$this->display($result);
			if($this->IsWordExists($result,'Store is invalid'))
			{
				$this->last_error_reason = "Store is invalid";
				return false;
			}
			else
			{			
				//PRESS 2 TABS
				$jdax->write(TAB,true); $jdax->write(TAB,true); $this->display($jdax->screen);

				//INPUT QTY
				$jdax->write(END,true);
				$jdax->write(BACKTAB,true);
				$jdax->write($avg_model_stock,true); 
				$this->display($jdax->screen);
				
				//INPUT QTY FOR ORDER AT
				$jdax->write(END,true);
				$jdax->write($order_at,true); 
				$this->display($jdax->screen);

				//INPUT QTY MAXIMUM STOCK
				$jdax->write(END,true);
				$jdax->write($maximum_stock,true); 
				$this->display($jdax->screen);

				//INPUT QTY DISPLAY MIN
				$jdax->write(END,true);
				$jdax->write($display_min,true); 
				$jdax->write(END,true);
				$this->display($jdax->screen);
				
				//Save
				$jdax->write(DOWN,true); $this->display($jdax->screen);
				$jdax->write(ENTER,true); $this->display($jdax->screen);				
				$jdax->write(F7,true); $this->display($jdax->screen);
				$jdax->write(F7,true); $this->display($jdax->screen);
				
				$result_prior = $jdax->screen;
				
				$jdax->write(F1,true);
				
				$result = $jdax->screen;
				$this->display($result);
				if($this->IsWordExists($result,"Has been placed on the JOBQ."))
				{
					$jdax->write(ENTER,true); $this->display($jdax->screen);
					//echo 'final screen';
					$jdax->write("23",true); $this->display($jdax->screen);
					return true;
				}
				else
				{
					//echo 'else final screen';
					/*
					if($this->IsWordExists($result,"Not On Replenishment"))
					{
						$this->display($jdax->screen);
					}*/
					$jdax->write("23",true); $this->display($jdax->screen);

					
					if($this->IsWordExists($result_prior,"Store is not on replenishment."))
					{
						return true;
					}
					else
					{
						$this->last_error_reason = 'Error on JDA maintenance. Please inform your system admin.'."\n".
						$this->displayForLog($result_prior)."\n".
						$this->displayForLog($result);
						return false;
					}
				}
				
				
				/*				
				if($this->IsWordExists($result,"AVG ORDER AT quantity cannot exceed AVG MODEL STOCK"))
				{
					$this->display($jdax->screen);
					$this->last_error_reason = " AVG ORDER AT quantity cannot exceed AVG MODEL STOCK";
					return false;
				}*/

			}
		}		
	}
			
	/*
	 * Use for displaying the jda screen
	 */
	function display($screen,$width=80){
		if($this->show_debug)
		{
			global $scrCounter;
			$scrCounter++;
			printf("%04d",$scrCounter);
			for($i = 4; $i<$width; $i++){
				echo "-";
			}
			echo "\n";
			print_r(chunk_split($screen,$width));
			
			for($i = 0; $i<$width; $i++){
				echo "-";
			}
			echo "\n";
		}
	}
	
	function displayForLog($screen,$width=80){
		global $scrCounter;
		$scrCounter++;
		printf("%04d",$scrCounter);
		for($i = 4; $i<$width; $i++){
			echo "-";
		}
		echo "\n";
		print_r(chunk_split($screen,$width));
		
		for($i = 0; $i<$width; $i++){
			echo "-";
		}
		echo "\n";
	}

	
	function retreat($times = 2,$marker="MENTB4"){
		global $jda; 
		$i = 0;
		while($i++ < $times && !$jda->screenWait($marker,1)){
			$jda->write(null,F1,true);		
			display($jda->screen,80);
			echo "pressed F1 to return";
		}
	}

	function set_timer($label){
		global $timers,$last_time,$start_time;
		$now_time = microtime(true);
		$interval = $now_time - $last_time;
		$elapsed = $now_time - $start_time;
		$last_time = $now_time;
		$timer = array("label"=>$label,"now" =>$now_time,"interval"=>$interval, "elapsed"=>$elapsed);
		$timers[] = $timer;
		return $timer;
	}
	
	/*
	 * Check if a word exist in the green screen. Used for validation
	 */
	function IsWordExists($result, $string_to_check)
	{
		if (strpos($result, $string_to_check) !== false) 
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>