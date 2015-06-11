<?php

date_default_timezone_set('Asia/Manila');

function mysql_credentials() {
	return array(
	    "hostname" => "localhost",
	    "user"     => "root",
	    "password" => 'root',
	    "db_name"  => "ccri"
	);
}

function jda_credentials() {
	if(isset($_SERVER['HTTP_HOST'])) {
        if (preg_match("/localhost*/",$_SERVER['HTTP_HOST']) || preg_match("/local.*/",$_SERVER['HTTP_HOST']))
			return array(
			    'jda_lib' => 'MMGSTLIB',
			    'user'	=> 'STRATPGMR',
			    'password' => 'PASSWORD'
			);

        switch($_SERVER['HTTP_HOST']) {
        case '172.16.100.92':
        // case '10.243.55.244':
        // case 'accountsdev-business.globe.com.ph':
			return array(
			    'jda_lib' => 'MMGSTLIB',
			    'user'	=> 'STRATPGMR',
			    'password' => 'PASSWORD'
			);
            break;
        default:
			return array(
			    'jda_lib' => 'MMGAPLIB',
			    'user'	=> 'STRATSYS',
			    'password' => 'PASSWORD'
			);
            break;
        }
    }
}