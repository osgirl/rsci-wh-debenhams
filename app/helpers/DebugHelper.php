<?php
/**
* Common debugging functions
* 
* @package 		SSI-WMS
* @subpackage 	Common
* @category    	Helpers
* @author 		Dean Francis Casili | fcasili2stratpoint.com | dean.casili@gmail.com
* @version 		Version 1.0
* 
*/
class DebugHelper {
    /**
    * uses vardump on a variable 
    * 
    * @param    (mixed)   $var      variable to be dumped
    * @param    (boolean) $do_log   write to log file
    * @param    (boolean) $is_die   execute php DIE
    **/
    public static function varDump($var,$is_die = TRUE,$do_log = TRUE,$die_marker = 'xxxDEBUGxxx')
    {
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
        if($do_log === TRUE) Log::info(__METHOD__ .' dump: '.print_r($var,true));
        if($is_die === TRUE) die($die_marker);
    }

    /**
    * uses vardump on a variable 
    * 
    * @param    (mixed)   $var      variable to be dumped
    * @param    (boolean) $do_log   write to log file
    * @param    (boolean) $is_die   execute php DIE
    **/
    public static function printR($method, $var,$is_die = TRUE,$do_log = TRUE,$die_marker = 'xxxDEBUGxxx')
    {
        if($is_die === TRUE) {
            echo "<pre>";
            print_r($var);
            echo "</pre>";
        }
        if($do_log === TRUE) Log::info($method .' dump: '.print_r($var,true));
        if($is_die === TRUE) die($die_marker);
    }

    /**
    * output sql statement 
    *     
    * @param    (boolean) $is_die   execute php DIE
    * @param    (boolean) $do_log   write to log file
    **/
    public static function outputSql($method, $is_die = TRUE, $do_log = TRUE) {
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        if($do_log === TRUE) Log::info($method .' dump: '.print_r($last_query,true));
        if($is_die) DebugHelper::printR($method, $last_query);
    }

    /**
    * Log sql statement
    *
    **/
    public static function log($method, $var="") {
        $queries = DB::getQueryLog();
        $last_query = end($queries);
        Log::info($method .' dump: '.print_r($last_query,true));
    }

    /**
    * Log variable 
    *
    **/
    public static function logVar($method, $var) {
        Log::info($method .' dump: '.print_r($var,true));
    }
}