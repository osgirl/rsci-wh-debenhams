<?php
	
	$active = 'local';

	$_DBCON['local']['hostname'] = 'localhost';
	$_DBCON['local']['username'] = 'root';
	$_DBCON['local']['password'] = '';
	$_DBCON['local']['database'] = 'atpx';

	$_DBCON['prod']['hostname'] = '172.16.1.83';
	$_DBCON['prod']['username'] = 'atpx_user';
	$_DBCON['prod']['password'] = 'atpxuser';
	$_DBCON['prod']['database'] = 'atpx';

	$_CONFIG['db'] = $_DBCON[$active];