<?php

include_once("receive_po.php");
include_once("close_po.php");
include_once("putaway_reserve.php");
include_once("letdown.php");
include_once("picklist.php");
include_once("palletizing_step1.php");
include_once("palletizing_step2.php");
include_once("palletizing_step3.php");
include_once("palletizing_step4.php");
include_once("palletizing_step5.php");
include_once("palletizing_step6.php");
include_once("store_receiving.php");


class jdaModules  
{	
	public function purchaseOrderReceiving()
	{	
		$receivePO = new poReceiving();
		$receiver_nos = $receivePO->getReceiverNo();
		print_r($receiver_nos);
		if(! empty($receiver_nos) ) 
		{
			$receivePO->enterUpToDockReceipt();
			foreach($receiver_nos as $receiver) {
				$validate = $receivePO->enterReceiverNumber($receiver);
				if($validate) $receivePO->enterPOForm($receiver);
			}
		}
		else {
			echo " \n No rows found!. \n";
			// return $receivePO->home();
		}
		
		// return $receivePO->home();
		return $receivePO->logout();
	}

	public function purchaseOrderClosing()
	{
		$closePO = new poClosing();
		$receiver_nos = $closePO->getReceiverNo();
		print_r($receiver_nos);
		if(! empty($receiver_nos) ) 
		{
			$closePO->enterUpToDockReceipt();
			foreach($receiver_nos as $receiver) {
				$validate = $closePO->enterReceiverNumber($receiver);
				if($validate)
				{
					$closePO->enterPOForm($receiver);
					$closePO->enterPoReceiptDetail();
					$closePO->enterPoReceiptDetailBySKU();
					//TODOS: need validation if qty is more than
					$closePO->enterQtyPerItem($receiver);
					$closePO->enterClosingPo();
					$closePO->enterJobName($receiver);
				}
			}
		}
		else {
			echo " \n No rows found!. \n";
		}


		$closePO->logout();
	}

	public function putAwayReserve()
	{
		$putawayReserve = new putawayReserve();

		$putawayReserve->enterUpToManualMoves();
		$skus = $putawayReserve->getSkus();
		$params = array();

		if(! empty($skus) ) 
		{
			foreach($skus as $value) 
			{
				$params = array(
						'toSlot' => $value['slot_id'],
						'sku'	 => $value['sku'],
						'quantity' => $value['quantity']
					);
				$putawayReserve->enterInventoryMovement();
				$putawayReserve->enterForm($params);
			}
		}

		$putawayReserve->logout();

	}

	public function letdownClosing()
	{
		$letdown = new letdown();

		$letdown->enterUpToLetdownMenu();

		$document_nos = $letdown->getDocumentNo();
		print_r($document_nos);
		if(! empty($document_nos) ) 
		{
			$params = array();
			foreach($document_nos as $document_no) {
				$letdown->enterEnterApproveLetdowns();
				
				$params = array('document_number' => $document_no);
				$letdown->enterApprovedLetdownQuantitiesForm();
				$validate = $letdown->enterForm($params);
				var_dump($validate);
				if($validate)
				{
					$letdown->enterUpdateDetail();
					$validateDetail = $letdown->enterFormDetails($params);
					var_dump($validateDetail);
					if($validateDetail) $letdown->submit($params);
				}
			}
		}

		$letdown->logout();
	}

	public function picklistClosing()
	{
		$picklist = new picklist();

		$getPicklist = $picklist->getPickNumber();
		print_r($getPicklist);
		
		if(! empty($getPicklist) ) 
		{
			$params = array();
			$picklist->enterUpToApprovePicksIntoCartons();

			foreach($getPicklist as $detail) 
			{
				$params = array(
							'document_number' => $detail['move_doc_number'], 
							'store_number'=> $detail['store_code'], 
							'carton_code'=> $detail['box_code']
						);
				$validate = $picklist->enterForm($params);
				if($validate)
				{
					$picklist->enterUpdateDetail();
					$validateDetail = $picklist->enterFormDetails($detail);
					if($validateDetail) 
					{
						$picklist->save($params);
					}
				}
			}
		}

		$picklist->logout();
	}

	public function maintainingCartonHeader()
	{
		$carton = new palletizingStep1();
		$carton->enterUpToCartonHeaderMaintenance();
		// $getBoxes = array("ASD110001");
		$getBoxes = $carton->getBoxes();
		print_r($getBoxes);
		if(! empty($getBoxes) ) 
		{
			foreach($getBoxes as $box) {
				$carton->save($box);
			}
		}

		return $carton->logout();
	}

	public function maintainingPalletHeader()
	{
		$pallete = new palletizingStep2();
		$pallete->enterUpToPalletHeaderMaintenance();
		// $getPallets = array("PLT0001", "PLT0002");
		$getPallets = $pallete->getPallets();
		if(! empty($getPallets) ) 
		{
			foreach($getPallets as $pallet) {
				$pallete->save($pallet);
			}
		}

		return $pallete->logout();
	}

	public function maintainingLoadHeader()
	{
		$loadHeader = new palletizingStep3();
		$loadHeader->enterUpToLoadHeaderMaintenance();


		// $getLoads = array('LOAD00010', 'LOAD00011');
		$getLoads = $loadHeader->getLoads();
		if(! empty($getLoads) ) 
		{
			foreach($getLoads as $load) 
			{
				$validate = $loadHeader->enterLoadControlNumber($load);		
				if($validate)
				{
					$validateDetail = $loadHeader->enterDetailForm($load);
					if($validateDetail) 
					{
						//get all location
						$getLocations = $loadHeader->getLocations($load);
						$getIds = array();
						foreach($getLocations as $location)
						{
							$isLocationValid = $loadHeader->enterLocation($location);
							// if($isLocationValid) $loadHeader->enterAnotherLocation();
							$getIds[] = $location['id'];
						}
						$loadHeader->save($getIds);
					}
				}
			}
		}

		return $loadHeader->logout();
	}

	public function assigningCartonToPallet()
	{
		$cartonToPallet = new palletizingStep4();
		$cartonToPallet->enterUpToSingle();

		// $getPallets = array('PLT000001', 'PLT000002');
		$getPallets = $cartonToPallet->getPallets();
		if(! empty($getPallets) ) 
		{
			foreach($getPallets as $pallet) 
			{
				$validate = $cartonToPallet->enterPalletId($pallet);
				if($validate) 
				{
					// $cartons = array('TXT000001', 'TXT000006');
					$cartons = $cartonToPallet->getCartons($pallet);
					$getIds = array();
					foreach($cartons as $carton)
					{
						$cartonToPallet->enterCartonId($carton);
						$getIds[] = $carton['id'];
					}

					$cartonToPallet->save($getIds, $pallet);
				}
			}
		}

		return $cartonToPallet->logout();
	}

	public function loading()
	{
		$loading = new palletizingStep5();
		$loading->enterUpToSingle();

		// $getPallets = array('PLT000001', 'PLT000002');
		$getLoads = $loading->getLoads();
		if(! empty($getLoads) ) 
		{
			foreach($getLoads as $load) 
			{
				$validate = $loading->enterBuildSingle($load);
				if($validate)
				{
					$getPallets = $loading->getPallets($load);
					$ids = array();
					foreach($getPallets as $pallet)
					{
						$isValidPallet = $loading->enterPalletId($pallet);
						if($isValidPallet) $loading->enterWeight($pallet);
						$ids[] = $pallet['id'];
					}
					$loading->save($ids, $load);
				}
			}
		}

		$loading->logout();
	}

	public function shipping()
	{
		$shipping = new palletizingStep6();
		$shipping->enterUpToShippingAgain();

		$getLoads = $shipping->getLoads();
		// $getLoads = array('LOAD00002');
		if(! empty($getLoads) ) 
		{
			foreach($getLoads as $load) 
			{
				$validate = $shipping->enterLoadId($load);
				if($validate) $shipping->save($load);
			}
		}

		$shipping->logout();
	}

	public function transfers()
	{
		$store = new storeReceiving();
		$store->enterUpToReceiveTransferCartons();

		$getBoxes = $store->getBoxes();
		if(! empty($getBoxes) ) 
		{
			foreach($getBoxes as $box) 
			{
				$store->enterCartonReceiving();
				$validate = $store->enterCartonId($box['box_code'], $box['store_code']);
				if($validate)
				{
					$validateDetail = $store->enterForm($box['box_code'], $box['store_code']);
					if($validateDetail) $store->save($box['box_code'], $box['store_code']);
				} 
			}
		}

		$store->logout();
	}
}