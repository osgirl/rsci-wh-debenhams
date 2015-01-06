<?php
/**
* Curl functions for HTTP GET, POSt
* 
* @package 		SSI-WMS
* @subpackage 	Common
* @category    	Helpers
* @author 		Dean Francis Casili | fcasili2stratpoint.com | dean.casili@gmail.com
* @version 		Version 1.0
* 
*/
class CurlHelper {
     /**
     * POST method for cURL.
     *
     * @param $url
     * @param $data     array   data
     * @param $header   string  access_token
     * @return array
     */
    public static function curlPost($url, $data, $access_token = NULL)
    {
        $url = cURL::buildUrl($url, $data);
        // DebugHelper::debugPrint($url);
        if( CommonHelper::hasValue($access_token) ) cURL::setHeader('Authorization', $access_token);

        $response = cURL::post($url, array('post'=>'data'));
        Log::info('info '. __METHOD__ . ' : '. json_encode($response));

        return array(
            'response' => json_decode($response->body, true), 
            'statusText' => $response->statusText,
            'status' => $response->statusCode
        );
    }

    /**
     * GET method for cURL.
     *
     * @param $url
     * @param $data
     * @param bool $auth
     * @param $type ("xml", "json")
     * @return array
     */
    public static function curlGet($url, $data = NULL, $access_token = NULL)
    {
        if( CommonHelper::hasValue($access_token) ) $data['access_token'] = $access_token;
        if( CommonHelper::arrayHasValue($data) ) $url = cURL::buildUrl($url, $data);

        $response = cURL::get($url);

        return array(
            'response' => json_decode($response->body, true),
            'statusText' => $response->statusText,
            'status' => $response->statusCode
        );
    }
}