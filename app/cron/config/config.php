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
	return array(
	    'jda_lib' => 'MMGAPLIB',
	    'user'	=> 'STRATSYS',
	    'password' => 'PASSWORD'
	);
}