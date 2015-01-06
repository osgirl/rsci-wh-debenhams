<?php

class LetdownSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		Letdown::truncate();
		LetdownDetails::truncate();

		Letdown::create(array(
		    "move_doc_number" => 900026
		));
		Letdown::create(array(
		    "move_doc_number" =>900027
		));
		Letdown::create(array(
		    "move_doc_number" =>900028
		));

		Letdown::create(array(
		    "move_doc_number" =>900029
		));

		Letdown::create(array(
		    "move_doc_number" => 900030
		));

		LetdownDetails::create(array(
			"sku"       		=> 'NGM0022',
			"store_code"			=> 'ST2',
		    "move_doc_number"	 => 900027,
		    "from_slot_code"	 => "PCK00001",
		    "quantity_to_letdown" 	 => 100
		));

		LetdownDetails::create(array(
			"sku"       => 'NGM0023',
			"store_code"			=> 'ST2',
		    "move_doc_number"	 => 900027,
		    "from_slot_code"	 => "PCK00001",
		    "quantity_to_letdown" 	 => 100
		));

		LetdownDetails::create(array(
			"sku"       => 'NGM0022',
			"store_code"			=> 'ST3',
		    "move_doc_number"	 => 900028,
		    "from_slot_code"	 => "PCK00001",
		    "quantity_to_letdown" 	 => 20
		));

		LetdownDetails::create(array(
			"sku"       => 'NGM0023',
			"store_code"			=> 'ST3',
		    "move_doc_number"	 => 900028,
		    "from_slot_code"	 => "PCK00001",
		    "quantity_to_letdown" 	 => 20
		));

		LetdownDetails::create(array(
			"sku"       => 'NGM0022',
			"store_code"			=> 'ST3',
		    "move_doc_number"	 => 900029,
		    "from_slot_code"	 => "PCK00001",
		    "quantity_to_letdown" 	 => 20
		));

		LetdownDetails::create(array(
			"sku"       => 'NGM0023',
			"store_code"			=> 'ST3',
		    "move_doc_number"	 => 900029,
		    "from_slot_code"	 => "PCK00001",
		    "quantity_to_letdown" 	 => 20
		));

		LetdownDetails::create(array(
			"sku"       => 'NGM0024',
			"store_code"			=> 'ST1',
		    "move_doc_number"	 => 900026,
		    "from_slot_code"	 => "PCK00001",
		    "quantity_to_letdown" 	 => 230
		));

		LetdownDetails::create(array(
			"sku"       => 'NGM0023',
			"store_code"			=> 'ST1',
		    "move_doc_number"	 => 900026,
		    "from_slot_code"	 => "PCK00001",
		    "quantity_to_letdown" 	 => 100
		));

		LetdownDetails::create(array(
			"sku"       => 'NGM0022',
			"store_code"			=> 'ST1',
		    "move_doc_number"	 => 900026,
		    "from_slot_code"	 => "PCK00001",
		    "quantity_to_letdown" 	 => 100
		));

		LetdownDetails::create(array(
			"sku"       => 'NGM0024',
			"store_code"			=> 'ST3',
		    "move_doc_number"	 => 900028,
		    "from_slot_code"	 => "PCK00001",
		    "quantity_to_letdown" 	 => 100
		));


		LetdownDetails::create(array(
			"sku"       => 'NGM0022',
			"store_code"			=> 'ST2',
		    "move_doc_number"	 => 900030,
		    "from_slot_code"	 => "PCK00002",
		    "quantity_to_letdown" 	 => 100
		));

		LetdownDetails::create(array(
			"sku"       => 'NGM0022',
			"store_code"			=> 'ST1',
		    "move_doc_number"	 => 900030,
		    "from_slot_code"	 => "PCK00002",
		    "quantity_to_letdown" 	 => 100
		));

		LetdownDetails::create(array(
			"sku"       => 'NGM0022',
			"store_code"			=> 'ST1',
		    "move_doc_number"	 => 900030,
		    "from_slot_code"	 => "PCK00001",
		    "quantity_to_letdown" 	 => 90
		));
	}

}