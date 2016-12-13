<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\ModelApiName;
use Response;

class ControllerApiUser extends Controller
{
    public function validateUser($username,$password) 
    {
        if(Auth::attempt(array('username' => $username, 'password' => $password, 'role_id' => '3')))
        {
            try 
            {
                $user = ModelApiName::GetApiUser(Auth::user()->id,$password);
                return Response::json(array('result' => $user),200);

            }
            catch(Exception $e) 
            {
                return Response::json(array(
                    "error" => true,
                    "result" => $e->getMessage()
                    ),400
                );
            }
        }
        else
        {
            return Response::json(array('result' => []),200);
        }
    }

  

 
    public function getVerifyValidateUser($username,$password) 
    {
        if(Auth::attempt(array('username' => $username, 'password' => $password, 'role_id' => '2')))
        {
            try 
            {
                $user = ModelApiName::GetApiUser(Auth::user()->id,$password);
                return Response::json(array('result' => $user),200);

            }
            catch(Exception $e) 
            {
                return Response::json(array(
                    "error" => true,
                    "result" => $e->getMessage()
                    ),400
                );
            }
        }
        else
        {
            return Response::json(array('result' => []),200);
        }
    }

  
}
