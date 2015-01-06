<?php

class ProductListsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
	
		ProductList::truncate();

		  ProductList::create(array(
		    "sku"=>902994,
		    "upc"=>902994,
		    "description"=>"OMGs DRKCHOCO CLSTR ALMDS&TOFE",
		    "short_description"=>"OMGSDARKCH",
		    "vendor"=>20100,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>205,
		    "set_code"=>7
		  ));
		  ProductList::create(array(
		    "sku"=>902995,
		    "upc"=>902995,
		    "description"=>"OMGs MILKCHOCO CLSTR ALMDS&TO",
		    "short_description"=>"OMGSMILKCH",
		    "vendor"=>20100,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>205,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903000,
		    "upc"=>903000,
		    "description"=>"HARIBO GOLDBEARS 30G",
		    "short_description"=>"HARIBOGOLD",
		    "vendor"=>20100,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>214,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903001,
		    "upc"=>903001,
		    "description"=>"HARIBO HAPPYCOLA 30G",
		    "short_description"=>"HARIBOHAPP",
		    "vendor"=>20100,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>214,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903002,
		    "upc"=>903002,
		    "description"=>"MIN MAID ORANGE250ML",
		    "short_description"=>"MINMAIDORA",
		    "vendor"=>20100,
		    "dept_code"=>200,
		    "sub_dept"=>210,
		    "class"=>206,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903003,
		    "upc"=>903003,
		    "description"=>"MIN MAID ORANGE800ML",
		    "short_description"=>"MINMAIDORA",
		    "vendor"=>20100,
		    "dept_code"=>200,
		    "sub_dept"=>210,
		    "class"=>206,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903004,
		    "upc"=>903004,
		    "description"=>"DM 4 SEASONS 1L",
		    "short_description"=>"DMSEASONSL",
		    "vendor"=>20100,
		    "dept_code"=>200,
		    "sub_dept"=>210,
		    "class"=>206,
		    "sub_class"=>204,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903005,
		    "upc"=>903005,
		    "description"=>"DEL MONTE MANGO 1L",
		    "short_description"=>"DELMONTEMA",
		    "vendor"=>20100,
		    "dept_code"=>200,
		    "sub_dept"=>210,
		    "class"=>206,
		    "sub_class"=>204,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903006,
		    "upc"=>903006,
		    "description"=>"TROPICANA MANGO355ML",
		    "short_description"=>"TROPICANAM",
		    "vendor"=>20100,
		    "dept_code"=>200,
		    "sub_dept"=>210,
		    "class"=>206,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903008,
		    "upc"=>903008,
		    "description"=>"DEL MONTE SPAGHETTI 175G",
		    "short_description"=>"DELMONTESP",
		    "vendor"=>20100,
		    "dept_code"=>200,
		    "sub_dept"=>270,
		    "class"=>239,
		    "sub_class"=>202,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>800195,
		    "upc"=>800195,
		    "description"=>"LIGHT CARROT SLICE A",
		    "short_description"=>"LIGHTCARRO",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>117,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>800196,
		    "upc"=>800196,
		    "description"=>"CHOCO BANANA WALNUT SLICE A",
		    "short_description"=>"CHOCOBANAN",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>117,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>800212,
		    "upc"=>800212,
		    "description"=>"PROMO-NESTLE JUICE 12OZ",
		    "short_description"=>"PROMONESTL",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>102,
		    "sub_class"=>102,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>800214,
		    "upc"=>800214,
		    "description"=>"VALUE MEAL-DRINK UPSIZE 16OZ",
		    "short_description"=>"VALUEMEALD",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>102,
		    "sub_class"=>102,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>800215,
		    "upc"=>800215,
		    "description"=>"VALUE MEAL-DRINK UPSIZE 22OZ",
		    "short_description"=>"VALUEMEALD",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>102,
		    "sub_class"=>102,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>800237,
		    "upc"=>800237,
		    "description"=>"CHOUX CREAM VANILLA",
		    "short_description"=>"CHOUXCREAM",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>109,
		    "sub_class"=>102,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>800238,
		    "upc"=>800238,
		    "description"=>"CHOUX CREAM CHOCOLATE",
		    "short_description"=>"CHOUXCREAM",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>109,
		    "sub_class"=>102,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>800239,
		    "upc"=>800239,
		    "description"=>"CHOUX CREAM MATCHA",
		    "short_description"=>"CHOUXCREAM",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>109,
		    "sub_class"=>102,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>800257,
		    "upc"=>800257,
		    "description"=>"WHITE CHOCO SUNDAE CONE",
		    "short_description"=>"WHITECHOCO",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>105,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>800258,
		    "upc"=>800258,
		    "description"=>"MIXED DARK WHITE SUNDAE CONE",
		    "short_description"=>"MIXEDDARKW",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>105,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>800259,
		    "upc"=>800259,
		    "description"=>"MIXED GTEA WHITE SUNDAE CONE",
		    "short_description"=>"MIXEDGTEAW",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>105,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903056,
		    "upc"=>903056,
		    "description"=>"TUNAPNDSAL MEP+LOWFATMLK250ML",
		    "short_description"=>"MILK250ML",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>108,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903057,
		    "upc"=>903057,
		    "description"=>"SPAM&EGGPNDSAL MEP+LOWFATMLK",
		    "short_description"=>"SPAMEGGPND",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>108,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903058,
		    "upc"=>903058,
		    "description"=>"VIENNASSAGECHSEPNDSLMEP+LOWFAT",
		    "short_description"=>"VIENNASSAG",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>108,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903059,
		    "upc"=>903059,
		    "description"=>"CHCKNPSTELPNDSAL MEP+LOWFATMLK",
		    "short_description"=>"CHCKNPSTEL",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>108,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903060,
		    "upc"=>903060,
		    "description"=>"DOLEBNNASNGLE+NSTLEFSMLON125G",
		    "short_description"=>"MELON125G",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>103,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903061,
		    "upc"=>903061,
		    "description"=>"DOLEBNNASNGLE+NSTLEFSSTRAW125G",
		    "short_description"=>"DOLEBNNASN",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>103,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903062,
		    "upc"=>903062,
		    "description"=>"DOLEBNNASNGLE+NSTLEFSMANGO125G",
		    "short_description"=>"DOLEBNNASN",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>103,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903063,
		    "upc"=>903063,
		    "description"=>"DOLEBNNASNGLE+NSTLEFSBUCNA125G",
		    "short_description"=>"DOLEBNNASN",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>103,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903064,
		    "upc"=>903064,
		    "description"=>"FCUP PINEPOMELOMELON+LOWFATMLK",
		    "short_description"=>"FCUPPINEPO",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903065,
		    "upc"=>903065,
		    "description"=>"FCUP PINEPOMELOPPAYA+LOWFATMLK",
		    "short_description"=>"FCUPPINEPO",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903066,
		    "upc"=>903066,
		    "description"=>"FCUP MLONHNYDEWCNTLOUPE+LWFATM",
		    "short_description"=>"FCUPMLONHN",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903067,
		    "upc"=>903067,
		    "description"=>"FCUP PINESTRAWPAPAYA+LOWFATMLK",
		    "short_description"=>"FCUPPINEST",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903068,
		    "upc"=>903068,
		    "description"=>"FCUP WHDRAGONMLONFUJI+LWFATMLK",
		    "short_description"=>"FCUPWHDRAG",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903069,
		    "upc"=>903069,
		    "description"=>"TUNAPNDESALMEP+NONFATMILK250ML",
		    "short_description"=>"TUNAPNDESA",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>108,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903070,
		    "upc"=>903070,
		    "description"=>"SPAM&EGGPNDESALMEP+NONFATMILK",
		    "short_description"=>"SPAMEGGPND",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>108,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903071,
		    "upc"=>903071,
		    "description"=>"VIENNASSAGECHSEPNDSLMEP+NONFAT",
		    "short_description"=>"VIENNASSAG",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>108,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903072,
		    "upc"=>903072,
		    "description"=>"CHCKNPSTELPNDESALMEP+NONFATMLK",
		    "short_description"=>"CHCKNPSTEL",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>110,
		    "class"=>108,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903073,
		    "upc"=>903073,
		    "description"=>"FCUP PINEPOMELOMELON+NONFATMLK",
		    "short_description"=>"FCUPPINEPO",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903074,
		    "upc"=>903074,
		    "description"=>"FCUP PINEPOMELOPAPAYA+NONFATMI",
		    "short_description"=>"FCUPPINEPO",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903075,
		    "upc"=>903075,
		    "description"=>"FCUP MLONHNYDEWCNTLOUPE+NONFAT",
		    "short_description"=>"FCUPMLONHN",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903076,
		    "upc"=>903076,
		    "description"=>"FCUP PINESTRAWPAPAYA+NONFATMLK",
		    "short_description"=>"FCUPPINEST",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903077,
		    "upc"=>903077,
		    "description"=>"FCUP WHDRAGONMLONFUJI+NONFATMK",
		    "short_description"=>"FCUPWHDRAG",
		    "vendor"=>20999,
		    "dept_code"=>100,
		    "sub_dept"=>120,
		    "class"=>116,
		    "sub_class"=>101,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900001,
		    "upc"=>900001,
		    "description"=>"ALASKAEVAP FILD370ML",
		    "short_description"=>"ALASKAEVAP",
		    "vendor"=>30001,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>228,
		    "sub_class"=>205,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900002,
		    "upc"=>900002,
		    "description"=>"ALASKA EVAP 370ML",
		    "short_description"=>"ALASKAEVAP",
		    "vendor"=>30001,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>228,
		    "sub_class"=>205,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900003,
		    "upc"=>900003,
		    "description"=>"ALASKA SCM 300ML",
		    "short_description"=>"ALASKASCMM",
		    "vendor"=>30001,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>228,
		    "sub_class"=>204,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900004,
		    "upc"=>900004,
		    "description"=>"CAR EVAP 370ML",
		    "short_description"=>"CAREVAPML",
		    "vendor"=>30001,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>228,
		    "sub_class"=>205,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900005,
		    "upc"=>900005,
		    "description"=>"CAR CONDENSADA 300ML",
		    "short_description"=>"CARCONDENS",
		    "vendor"=>30001,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>228,
		    "sub_class"=>204,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900006,
		    "upc"=>900006,
		    "description"=>"ALPNSTRLZCRMILK200ML",
		    "short_description"=>"ALPNSTRLZC",
		    "vendor"=>30001,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>228,
		    "sub_class"=>205,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900007,
		    "upc"=>900007,
		    "description"=>"ALASKA CHOCO SLIM PACK 236ML",
		    "short_description"=>"ALASKACHOC",
		    "vendor"=>30001,
		    "dept_code"=>200,
		    "sub_dept"=>210,
		    "class"=>202,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900008,
		    "upc"=>900008,
		    "description"=>"ALASKA SWT MILK SLIM PCK 236ML",
		    "short_description"=>"ALASKASWTM",
		    "vendor"=>30001,
		    "dept_code"=>200,
		    "sub_dept"=>210,
		    "class"=>207,
		    "sub_class"=>204,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900009,
		    "upc"=>900009,
		    "description"=>"ALASKA YOGHURT STRWBRY 180ML",
		    "short_description"=>"ALASKAYOGH",
		    "vendor"=>30001,
		    "dept_code"=>200,
		    "sub_dept"=>220,
		    "class"=>213,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900010,
		    "upc"=>900010,
		    "description"=>"ALASKA YOGHURT BLUEBRY 180ML",
		    "short_description"=>"ALASKAYOGH",
		    "vendor"=>30001,
		    "dept_code"=>200,
		    "sub_dept"=>220,
		    "class"=>213,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900011,
		    "upc"=>900011,
		    "description"=>"ALASKA CREMA 250ML",
		    "short_description"=>"ALASKACREM",
		    "vendor"=>30001,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>228,
		    "sub_class"=>205,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900012,
		    "upc"=>900012,
		    "description"=>"ALASKA PWDRD MILK DRNK BX 150G",
		    "short_description"=>"ALASKAPWDR",
		    "vendor"=>30001,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>228,
		    "sub_class"=>207,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>903007,
		    "upc"=>903007,
		    "description"=>"ALASKA CHOCOLATE 20G",
		    "short_description"=>"ALASKACHOC",
		    "vendor"=>30001,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>228,
		    "sub_class"=>206,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900013,
		    "upc"=>900013,
		    "description"=>"POTCHI STRWBRY SNACK PACK 25G",
		    "short_description"=>"POTCHISTRW",
		    "vendor"=>30002,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>214,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900014,
		    "upc"=>900014,
		    "description"=>"POTCHI GUMMY WORMS 25G",
		    "short_description"=>"POTCHIGUMM",
		    "vendor"=>30002,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>214,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900015,
		    "upc"=>900015,
		    "description"=>"FRUTOS CHEWY FRUIT 25G",
		    "short_description"=>"FRUTOSCHEW",
		    "vendor"=>30002,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>214,
		    "sub_class"=>208,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900016,
		    "upc"=>900016,
		    "description"=>"FRUTOS CHEWY SOUR 25G",
		    "short_description"=>"FRUTOSCHEW",
		    "vendor"=>30002,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>214,
		    "sub_class"=>208,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900017,
		    "upc"=>900017,
		    "description"=>"POTCHI SOUR GUMMY FRUITS 25G",
		    "short_description"=>"POTCHISOUR",
		    "vendor"=>30002,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>214,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900018,
		    "upc"=>900018,
		    "description"=>"POTCHI GUMMY BEARS 25G",
		    "short_description"=>"POTCHIGUMM",
		    "vendor"=>30002,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>214,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900019,
		    "upc"=>900019,
		    "description"=>"VAN HOUTEN FRUIT NUT WHOLE 38G",
		    "short_description"=>"VANHOUTENF",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900020,
		    "upc"=>900020,
		    "description"=>"VAN HOUTEN ALMOND WHOLE 38G",
		    "short_description"=>"VANHOUTENA",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900021,
		    "upc"=>900021,
		    "description"=>"HERSHEYS TREATS CHOCO ROLL 16G",
		    "short_description"=>"HERSHEYSTR",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>209,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900022,
		    "upc"=>900022,
		    "description"=>"HERSHEYS TREATS COOKIE BAR 18G",
		    "short_description"=>"HERSHEYSTR",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>209,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900023,
		    "upc"=>900023,
		    "description"=>"HERSHEYS TREATS CRNCHY BAR 18G",
		    "short_description"=>"HERSHEYSTR",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>209,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900024,
		    "upc"=>900024,
		    "description"=>"HERSHEYS MILK CHOCO KISS 36G",
		    "short_description"=>"HERSHEYSMI",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>204,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900025,
		    "upc"=>900025,
		    "description"=>"HERSHEYS ALM KISS 36G",
		    "short_description"=>"HERSHEYSAL",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>204,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900026,
		    "upc"=>900026,
		    "description"=>"HERSHEYS MILK CHOCO KISS 43G",
		    "short_description"=>"HERSHEYSMI",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>204,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900027,
		    "upc"=>900027,
		    "description"=>"HERSHEYS COOKIESNCRM KISS 36G",
		    "short_description"=>"HERSHEYSCO",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>204,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900028,
		    "upc"=>900028,
		    "description"=>"HERSHEYS MILK CHOCO BAR 43G",
		    "short_description"=>"HERSHEYSMI",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900029,
		    "upc"=>900029,
		    "description"=>"HERSHEYS ALMOND BAR 41G",
		    "short_description"=>"HERSHEYSAL",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900030,
		    "upc"=>900030,
		    "description"=>"HERSHEYS COOKIESNCRM BAR 40G",
		    "short_description"=>"HERSHEYSCO",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900031,
		    "upc"=>900031,
		    "description"=>"HERSHEYS CRMY MILK BAR 40G",
		    "short_description"=>"HERSHEYSCR",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900032,
		    "upc"=>900032,
		    "description"=>"HERSHEYS CRMY ALM BAR 40G",
		    "short_description"=>"HERSHEYSCR",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900033,
		    "upc"=>900033,
		    "description"=>"HERSHEYS NUGGET MILK 3P 28G",
		    "short_description"=>"HERSHEYSNU",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>202,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900034,
		    "upc"=>900034,
		    "description"=>"HERSHEYS NUGGET ALMOND3P 28G",
		    "short_description"=>"HERSHEYSNU",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>202,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900035,
		    "upc"=>900035,
		    "description"=>"HERSHEYS NUGGET CKSNCRM 3P 28G",
		    "short_description"=>"HERSHEYSNU",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>202,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>902717,
		    "upc"=>902717,
		    "description"=>"HERSHEY ALMOND MINIBAR 23G",
		    "short_description"=>"HERSHEYALM",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>902718,
		    "upc"=>902718,
		    "description"=>"HERSHEY CNC MINIBAR 23G",
		    "short_description"=>"HERSHEYCNC",
		    "vendor"=>30003,
		    "dept_code"=>200,
		    "sub_dept"=>230,
		    "class"=>215,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>902784,
		    "upc"=>902784,
		    "description"=>"TANG MANGO 25G",
		    "short_description"=>"TANGMANGOG",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>227,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900036,
		    "upc"=>900036,
		    "description"=>"TANG PINEAPPLE25G",
		    "short_description"=>"TANGPINEAP",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>227,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900037,
		    "upc"=>900037,
		    "description"=>"TANG ORANGE 30G",
		    "short_description"=>"TANGORANGE",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>227,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));
		  ProductList::create(array(
		    "sku"=>900038,
		    "upc"=>900038,
		    "description"=>"TANG LITRO APPLE 30G",
		    "short_description"=>"TANGLITROA",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>227,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		  ));

		ProductList::create(array(
		    "sku"=>900039,
		    "upc"=>900039,
		    "description"=>"TANG STRAWBERRY 25G",
		    "short_description"=>"TANGSTRAWB",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>227,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		    
		));	
		ProductList::create(array(
		    "sku"=>900040,
		    "upc"=>900040,
		    "description"=>"TANG ICETEALEMON 25G",
		    "short_description"=>"TANGICETEA",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>227,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		    
		));	
		ProductList::create(array(
		    "sku"=>900041,
		    "upc"=>900041,
		    "description"=>"TANG ICETEA APPLE35G",
		    "short_description"=>"TANGICETEA",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>227,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		    
		));	
		ProductList::create(array(
		    "sku"=>900042,
		    "upc"=>900042,
		    "description"=>"TANG 4SEASONS 25G",
		    "short_description"=>"TANGSEASON",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>250,
		    "class"=>227,
		    "sub_class"=>203,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		    
		));	
		ProductList::create(array(
		    "sku"=>900043,
		    "upc"=>900043,
		    "description"=>"OREO SANDWICH VANILLA 137 G",
		    "short_description"=>"OREOSANDWI",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>260,
		    "class"=>230,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		    
		));	
		ProductList::create(array(
		    "sku"=>900044,
		    "upc"=>900044,
		    "description"=>"OREO SAND DBL STUF 152.4 G",
		    "short_description"=>"OREOSANDDB",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>260,
		    "class"=>230,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		    
		));	
		ProductList::create(array(
		    "sku"=>900045,
		    "upc"=>900045,
		    "description"=>"CHIPS AHOY 85.5G",
		    "short_description"=>"CHIPSAHOYG",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>260,
		    "class"=>230,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		    
		));	
		ProductList::create(array(
		    "sku"=>900046,
		    "upc"=>900046,
		    "description"=>"OREO 29.4G SEA",
		    "short_description"=>"OREOGSEA",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>260,
		    "class"=>230,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		    
		));	

		ProductList::create(array(
		    "sku"=>900047,
		    "upc"=>900047,
		    "description"=>"OREO SANDWICH CHOCO 137G",
		    "short_description"=>"OREOSANDWI",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>260,
		    "class"=>230,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		    
		));	
		ProductList::create(array(
		    "sku"=>900048,
		    "upc"=>900048,
		    "description"=>"CHIPS AHOY 38G",
		    "short_description"=>"CHIPSAHOYG",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>260,
		    "class"=>230,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		    
		));	

		ProductList::create(array(
		    "sku"=>900049,
		    "upc"=>900049,
		    "description"=>"CHIPS AHOY 266G",
		    "short_description"=>"CHIPSAHOYG",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>260,
		    "class"=>230,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		    
		));	

		ProductList::create(array(
		    "sku"=>900050,
		    "upc"=>900050,
		    "description"=>"TIGER ENERGY 50.4 G",
		    "short_description"=>"TIGERENERG",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>260,
		    "class"=>230,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		    
		));	

		ProductList::create(array(
		    "sku"=>900051,
		    "upc"=>900051,
		    "description"=>"OREO VANILLA 19.6G",
		    "short_description"=>"OREOVANILL",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>260,
		    "class"=>230,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		    
		));	

		ProductList::create(array(
			"sku"=>900052,
			"upc"=>900052,
		    "description"=>"OREO VANILLA 29.4G",
		    "short_description"=>"OREOVANILL",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>260,
		    "class"=>230,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));	

		ProductList::create(array(
			"sku"=>900053,
			"upc"=>900053,
		    "description"=>"CHEEZ WHIZ SULIT15G",
		    "short_description"=>"CHEEZWHIZS",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));

		ProductList::create(array(
			"sku"=>'NGM0001',
			"upc"=>'NGM0001',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));

		ProductList::create(array(
			"sku"=>'NGM0002',
			"upc"=>'NGM0002',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));

		ProductList::create(array(
			"sku"=>'NGM0003',
			"upc"=>'NGM0003',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));

		ProductList::create(array(
			"sku"=>'NGM0004',
			"upc"=>'NGM0004',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		
		ProductList::create(array(
			"sku"=>'NGM0005',
			"upc"=>'NGM0005',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));

		ProductList::create(array(
			"sku"=>'NGM0006',
			"upc"=>'NGM0006',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));

		ProductList::create(array(
			"sku"=>'NGM0007',
			"upc"=>'NGM0007',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));

		ProductList::create(array(
			"sku"=>'NGM0008',
			"upc"=>'NGM0008',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));

		ProductList::create(array(
			"sku"=>'NGM0009',
			"upc"=>'NGM0009',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));

		ProductList::create(array(
			"sku"=>'NGM0010',
			"upc"=>'NGM0010',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));

		ProductList::create(array(
			"sku"=>'NGM0011',
			"upc"=>'NGM0011',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0012',
			"upc"=>'NGM0012',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0013',
			"upc"=>'NGM0013',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0014',
			"upc"=>'NGM0014',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0015',
			"upc"=>'NGM0015',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0016',
			"upc"=>'NGM0016',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0017',
			"upc"=>'NGM0017',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0018',
			"upc"=>'NGM0018',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0019',
			"upc"=>'NGM0019',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0020',
			"upc"=>'NGM0020',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0021',
			"upc"=>'NGM0021',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0022',
			"upc"=>'NGM0022',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0023',
			"upc"=>'NGM0023',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0024',
			"upc"=>'NGM0024',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));

		ProductList::create(array(
			"sku"=>'NGM0025',
			"upc"=>'NGM0025',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0026',
			"upc"=>'NGM0026',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0027',
			"upc"=>'NGM0027',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0028',
			"upc"=>'NGM0028',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0029',
			"upc"=>'NGM0029',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0030',
			"upc"=>'NGM0030',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
		ProductList::create(array(
			"sku"=>'NGM0031',
			"upc"=>'NGM0031',
		    "description"=>"TEST UPC",
		    "short_description"=>"TestUpc",
		    "vendor"=>30004,
		    "dept_code"=>200,
		    "sub_dept"=>280,
		    "class"=>244,
		    "sub_class"=>201,
		    "set_code"=>0
		    ,"created_at" => date('Y-m-d H:i:s')
		));
	}

}