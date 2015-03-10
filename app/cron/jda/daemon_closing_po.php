#!/usr/local/bin/php -q
<?php

include_once("jda_modules.php");

$module = new jdaModules();

$validate = $module->purchaseOrderReceiving();
if($validate) $module->purchaseOrderClosing();
else echo 'Something went wrong...';
