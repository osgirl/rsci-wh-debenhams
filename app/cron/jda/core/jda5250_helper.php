<?php
chdir(dirname(__FILE__));
include_once('../../config/config.php');
include_once("jda5250.php");

class jdaCustomClass 
{	
	public static $jda;
	public static $scrCounter;
	public static $timers = array();
	public static $start_time;
	public static $lastTime;
	public static $successFlag = 1; // upon success syncing
	public static $errorFlag = 2; // when error occured during jda syncing

	public static function login()
	{
		$creds = jda_credentials();
		echo "core/jdahelper5250: ".$creds['jda_lib']. "\n";

		self::$start_time = microtime(true);
		self::$lastTime = microtime(true);

		echo "Connecting to JDA \n";
		self::$jda = new jdatelnet("172.16.1.1",3);
		self::$jda->screenWait("Password");
		self::$jda->login($creds['user'],$creds['password'],$creds['jda_lib']);
	}

	public static function display($screen,$width=132){
		self::$scrCounter;
		self::$scrCounter++;
		printf("%04d",self::$scrCounter);
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

	public static function set_timer($label){
		$now_time = microtime(true);
		$interval = $now_time - self::$last_time;
		$elapsed = $now_time - self::$start_time;
		self::$last_time = $now_time;
		$timer = array("label"=>$label,"now" =>$now_time,"interval"=>$interval, "elapsed"=>$elapsed);
		$timers[] = self::$timer;
		return self::$timer;
	}

	public static function show_timers()
	{
		self::$timers;
		printf("%20s%10s%10s%10s\n","Label", "Interval","Interval","Elapsed");
		foreach(self::$timers as self::$timer){
			extract(self::$timer);
			$mins = floor($interval / 60);
			$secs = $interval % 60;
			$interval_mins = "$mins:$secs";
			printf("%20s%10d%10s%10d\n",$label, $interval,$interval_mins,$elapsed);
		}
	}

	public static function checkRecoverJob()
	{
		#check if user entered this screen
		if(self::$jda->screenCheck("Attempt to Recover Interactive Job")) {
			echo " Resetting...\n";
			self::$jda->write5250(array(array("90",22,07)),ENTER,true);
			$params=array();
			$params[] = array(6,38,"YES ");
			$params[] = array(7,38,"YES");
			self::$jda->write5250($params,ENTER,true);
			echo " Done!\n";
			self::$jda->close();	
			# login again
			self::login();
		}
	}

	public static function checkJobOnProgress()
	{
		#happens when a job is currently performed with the same account
		self::display(self::$jda->screen,132);
		self::$jda->screenWait("Press Enter to continue");
		if(self::$jda->screenCheck("Press Enter to continue")) {
			echo 'saw enter to cont';
			self::$jda->write5250(null,ENTER,true);
		}
	}

	public static function enterDistributionManagement()
	{
		self::$jda->screenWait("Distribution Center Management");
		self::display(self::$jda->screen,132);	
		self::$jda->write5250(array(array("13",22,44)),ENTER,true);
		echo "Entered: Distribution Center Management \n";
	}

	public static function pressEnter()
	{
		self::$jda->screenWait("Press {ENTER}");
		self::display(self::$jda->screen,132);	
		self::$jda->write5250(NULL,ENTER,true);
		echo "Entered: Pressed Enter Key \n";	
	}

	public static function pressF1()
	{
		self::$jda->screenWait("F1=Return");
		self::display(self::$jda->screen,132);	
		self::$jda->write5250(NULL,F1,true);
		echo "Entered: Pressed F1 Key \n";	
	}

	public static function pressF7()
	{
		self::$jda->screenWait("F7=Close");
		self::display(self::$jda->screen,132);	
		self::$jda->write5250(NULL,F7,true);
		echo "Entered: Pressed F7 Key \n";	
	}

	public static function pressF10()
	{
		self::$jda->screenWait("F10=Submit");
		self::display(self::$jda->screen,132);
		self::$jda->write5250(NULL,F10,true);
		echo "Entered: Pressed F10 Key \n";
	}

	public static function enterWarning()
	{
		self::$jda->screenWait("You have requested to Exit");
		self::display(self::$jda->screen,132);	
		self::$jda->write5250(NULL,F1,true);
		echo "Entered: Warning Message \n";	
	}

	#box/palletizing
	public static function enterWarehouseMaintenance()
	{
		self::$jda->screenWait("Warehouse Maintenance");
		self::display(self::$jda->screen,132);
		self::$jda->write5250(array(array("04",22,44)),ENTER,true);
		echo "Entered: Warehouse Maintenance \n";	
	}

	#box/palletizing
	public static function enterCartonPalletLoadMaintenance()
	{
		self::$jda->screenWait("Load Maintenance");
		self::display(self::$jda->screen,132);
		self::$jda->write5250(array(array("10",22,44)),ENTER,true);
		echo "Entered: Carton Pallet Load Maintenance \n";	
	}

	/*public function pressF1By7Times()
	{	
		# F1 to Return
		self::checkRecoverJob();
		$tries = 0;
		while($tries++ < 7){
			echo "\n Press F1 & tries: {$tries} \n";
			self::$jda->set_pos(6,8);
			self::display(self::$jda->screen,132);
			self::$jda->write5250(null,F1,true);
			if(self::$jda->screenCheck("Program Messages"))
			{
				self::$jda->write5250(null,F12,true);
			}
		}	

		self::show_timers();
		echo "End\n";
	}*/
	
	public function logout()
	{	
		# F1 to Return
		$tries = 0;
		while($tries++ < 5 && !self::$jda->screenCheck("F7=Signoff")){
			echo "\n F7 not found & tries: {$tries} \n";
			self::$jda->set_pos(6,8);
			self::$jda->write5250(null,F1,true);

			/*if(self::$jda->screenCheck("Program Messages - Help")) 
			{
				self::$jda->write5250(null,F3,true);
				echo "Entered: Program Messages \n";
				if(self::$jda->screenCheck("Display Program Messages"))
				{
					self::$jda->write5250(null,ENTER,true);
					echo "Entered: Display Program Messages \n";
					break;
				}
			}*/
		}	
		echo "F1 to return\n";
		self::display(self::$jda->screen,132);
		# F7 to signoff
		self::$jda->write5250(null,F7,true);				// Enter F7 to signoff
		echo "F7 to signoff\n";
		self::display(self::$jda->screen,132);		
		self::$jda->close();			

		self::show_timers();
		echo "End\n";
	}

	public static function logError($error_message, $method) {
		#format: [2014-05-22 07:05:17] ERROR: SlotDetails::getSlotDetailsMain with message "message"
		$date = date('Y-m-d H:i:s');
		$errorMsgFormat = "[{$date}] ERROR: {$method} with message \"{$error_message}\" \n";
		error_log("{$errorMsgFormat}", 3, dirname(__FILE__)."/../logs/jda-errors.log");
	}
	
}
