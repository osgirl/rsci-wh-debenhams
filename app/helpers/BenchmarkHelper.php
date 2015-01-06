<?php
/**
* Benchmark Helper
* 
* @package 		SSI-eWMS
* @subpackage 	Common
* @category    	Helpers
* @author 		Joanna Lee | jlee@stratpoint.com | joannalee0912@gmail.com
* @version 		Version 1.0
* 
*/
class BenchmarkHelper {
    protected $start_time;

    protected $end_time;

    protected $method;

    /**
     * Sets start time
     *
     */
    public function start($method)
    {
        $this->method = $method;
        $this->start_time = microtime(true); 
    }

    /**
     * Sets end time
     *
     */
    public function end()
    {
        $this->end_time = microtime(true);
        $this->logTime();
    }

    /**
     * Logs time
     *
     */
    public function logTime()
    {
        $time = $this->getTime();
        Log::info(date('m d Y h:i:s A'). '['.$this->method . ']- '. $time);
    }

    /**
     * get time
     *
     */
    public function getTime()
    {
        $time = $this->end_time - $this->start_time;
        return $time;
    }

}