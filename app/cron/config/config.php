<?php

date_default_timezone_set('Asia/Manila');

 function mysql_credentials() {
	return array(
	    "hostname" => "localhost",
	    "user"     => "root",
	    "password" => '',
	    "db_name"  => "deve"
	);
}

 function jda_credentials() {
	return array(
	    'jda_lib' => 'MMRSTLIB',
	    'user'	=> 'DEBSPGMR',
	    'password' => 'PASSWORD'
	);
}
?>