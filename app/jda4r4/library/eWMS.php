<?php

class eWMS
{
	protected $db;

	public function __construct($hostname, $username, $password, $database) 
	{
		$this->db = new mysqli($hostname, $username, $password, $database);		
	}
	
	public function GetPendingPO()
	{
		$db = $this->db;
		$result = $db->query("SELECT * FROM wms_purchase_order_lists ");
		if($result === false) 
		{
			return false;
		} 
		else 
		{	
			//return mysqli_fetch_assoc($result);
			$result1 = array();
			foreach ($result as $value ) {
				$result1[] = $value['receiver_no'];
			}

		return $result1;		
		}
	}


	public function UpdatePOQty()
	{
		$db = $this->db;
		$result = $db->query("SELECT * FROM death where id=7 or id=8");
		if($result === false) {
			return false;
		} else {	
		return mysqli_fetch_assoc($result);		
		}
	}

	/**
	 * retrieve the connection for specific jda instance
	 */
	public function GetJdaConnection($connection_id)
	{		
		$db = $this->db;
		$result = $db->query("SELECT * FROM jda_connections WHERE id={$connection_id}");
		if($result === false) {
			return false;
		} else {
			return mysqli_fetch_assoc($result);
		}
	}

	public function PickUpToForAutoTLProcess($osp_site_id)
	{
		$db = $this->db;
		$result = $db->query("SELECT * FROM autotl_queues WHERE osp_site_id={$osp_site_id} AND status=1 AND ready=1 LIMIT 0,1");
		if($result === false) {
			return false;
		} else {
			return mysqli_fetch_assoc($result);
			/*
			$queue = mysqli_fetch_assoc($result);
			$result = $db->query("UPDATE autotl_queues SET status=2 WHERE id={$queue['id']}");
			if($result !== false) {
				return $queue;
			} else {
				return false;
			}
			*/
		}
	}

}