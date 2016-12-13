<?php
/**
* Common functions such as random numbers, string manipulations/validations
*
* @package 		SSI-WMS
* @subpackage 	Common
* @category    	Helpers
* @author 		Dean Francis Casili | fcasili2stratpoint.com | dean.casili@gmail.com
* @version 		Version 1.0
*
*/
class CommonHelper {
    /**
    * check variable is empty
    *
    * @param 	$var		variable to be analyzed
    * @return 	(boolean)
    **/
    public static function hasValue($var)
    {
    	if(!isset($var))
    	{
    		return FALSE;
    	} else
    	{
            if(is_null($var) || empty($var))
            {
                return FALSE;
            } else
            {
                return TRUE;
            }
    	}
    }



    public static function filternator($query,$arrparam=array(),$limit,$getcount=false)
    {
        $x=0;
        foreach ($arrparam as $key => $filcol) 
        {
           $subkey= substr($key,7);
            if ($x<$limit)
            {
               if( CommonHelper::hasValue($filcol) && CommonHelper::hasValue($key) ) $query->where(''.$subkey.'', 'LIKE', '%'. $filcol .'%');
            }
            if ($key=='sort')$sort=$filcol;
            if ($key=='order')$order=$filcol;
            if ($key=='page')$page=$filcol;
            $x++;
        }
        if($getcount) return count($query);
        
        if( CommonHelper::hasValue($sort) && CommonHelper::hasValue($order))  
        {
            
        }
        if(CommonHelper::hasValue($page))
        {
            $query->skip(30 * ($page - 1))
                  ->take(30);
        }
        
        return $query;
    }

        public static function pagenator($query,$page)
    {
        if(CommonHelper::hasValue($page))
        {
            $query->skip(30 * ($page - 1))
                  ->take(30);
        }
        return $query;
    }

    /**
    * checks if variable is a valid array and not empty
    *
    * @param    $arr        array variable to be analyzed
    * @return   (boolean)
    **/
    public static function arrayHasValue($arr)
    {
        if(!isset($arr) && !is_array($arr))
        {
            return FALSE;
        } else
        {
            if(count($arr) === 0 || empty($arr))
            {
                return FALSE;
            } else
            {
                return TRUE;
            }
        }
    }

    /**
    * returns null if the variable is empty
    *
    * @param    $var        variable to be analyzed
    * @return   (mixed)
    **/
    public static function assess_variable_value($var)
    {
        return (has_value($var) ? $var : NULL);
    }

    /**
    * check variable is numeric and has value
    *
    * @param    $var        variable to be analyzed
    * @return   (boolean)
    **/
    public static function numericHasValue($var)
    {
        if(isset($var) && is_numeric($var))
        {
            return TRUE;
        } else
        {
            return FALSE;
        }
    }

    /**
    * recursively converts object to array
    *
    * @param (object)   $data   object to be converted to array
    * @return (object)
    **/
    public static function object_to_array($data)
    {
        if(is_array($data) || is_object($data)) {
            $result = array();
            foreach($data as $key => $value) {
                $result[$key] = CommonHelper::object_to_array($value);
            }
            return $result;
        }
        return $data;
    }

    /**
    * searches a specific array item based on key
    *
    * @param (string)   $item           the needle
    * @param (string)   $array_key      key of the array to be compared with the needle
    * @param (string)   $array_items    the haystak
    * @return (array)
    **/
    public static function get_item_from_array($item,$array_key,$array_items)
    {
        if(! CommonHelper::arrayHasValue($array_items)) return array();
        if(! CommonHelper::hasValue($item)) return array();
        $array_val = array();
        foreach($array_items as $array_item)
        {
            if($array_item[$array_key] == $item)
            {
                $array_val = $array_item;
                break;
            }
        }
        return $array_val;
    }

    /**
    * checks if the value is in the array
    *
    * @param (string)   $item           the needle
    * @param (string)   $array_items    the haystak
    * @return (boolean)
    **/
    public static function valueInArray($item, $array_items)
    {
        // print_r()
        if(! CommonHelper::arrayHasValue($array_items)) return FALSE;

        if (in_array($item, $array_items)) {
        	return TRUE;
        } else {
        	return FALSE;
        }
    }


    public static function setRequiredFields($required_fields = array()) {
        if(self::arrayHasValue($required_fields))
        {
            foreach($required_fields as $value)
            {
                $tmp_val = Input::get($value);
                if(!self::hasValue($tmp_val)) throw new Exception("Missing {$value} parameter");
            }
        }
    }

    public static function return_success_message($message)
    {
        return Response::json(array(
                'error' => false,
                'message' => 'Success',
                'result' => $message),
                200
        );
    }

    public static function return_success()
    {
        return Response::json(array(
                'error' => false,
                'message' => 'Success'),
                200
        );
    }


    public static function return_fail($message)
    {
        return Response::json(array(
                "error" => true,
                "result" => $message),
                400
        );
    }

    /**
    * Execute command in the background without PHP waiting for it to finish for Unix
    *
    * @example  Commonhelper::execInBackground();
    *
    * @param  $cmd       string command to execute
    * @return
    */
    public static function execInBackground($cmd,$source)
    {
        $cmd = 'php -q ' . __DIR__.'/../../app/cron/jda/' . $cmd;
        $filename=$source . "_" . date('m_d_y');
        $outputfile = __DIR__.'/../../app/cron/jda/logs/'.$filename.'.log';
    	$pidfile = __DIR__.'/../../app/cron/jda/logs/pidfile.log';

        exec(sprintf("%s >> %s 2>&1 & echo $! >> %s", $cmd, $outputfile, $pidfile));
        // exec($cmd . " >/dev/null 2> /dev/null & echo $!");

        // exec($cmd . " > /dev/null &");
    }

    /**
    * Execute command in the background without PHP waiting for it to finish for Unix
    *
    * @example  Commonhelper::execInBackground();
    *
    * @param  $cmd       string command to execute
    * @return
    */
    public static function archiveLogs()
    {
        $file       = __DIR__.'/../../archive_logs/audit_trail_'.time().'.sql';
        $cmd        = 'mysqldump -uroot -proot ccri wms_audit_trail > ' . $file;
        $outputfile = __DIR__.'/../../archive_logs/output.log';
        $pidfile    = __DIR__.'/../../archive_logs/pidfile.log';

        exec($cmd, $outputfile, $pidfile);

        if($pidfile) {
            return FALSE;
        }

        return TRUE;
    }

    /*
     * Method to strip tags globally.
     */
    public static function globalXssClean()
    {
        // Recursive cleaning for array [] inputs, not just strings.
        $sanitized = static::arrayStripTags(Input::get());
        Input::merge($sanitized);
    }

    public static function arrayStripTags($array)
    {
        $result = array();

        foreach ($array as $key => $value) {
            // Don't allow tags on key either, maybe useful for dynamic forms.
            $key = strip_tags($key);

            // If the value is an array, we will just recurse back into the
            // function to keep stripping the tags out of the array,
            // otherwise we will set the stripped value.
            if (is_array($value)) {
                $result[$key] = static::arrayStripTags($value);
            } else {
                // I am using strip_tags(), you may use htmlentities(),
                // also I am doing trim() here, you may remove it, if you wish.
                $result[$key] = trim(strip_tags($value));
            }
        }

        return $result;
    }

}
