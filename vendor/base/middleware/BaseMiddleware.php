<?php

/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/9
 * Time: 16:37
 */

namespace Base\Middleware;

use Base\Controller\BaseController;
use Base\Http\Request;
use Base\Http\Response;
use Base\Tool\Jwt;

class BaseMiddleware
{

    public function handle(){

     }

    public function jsonSuccess($data=[],$msg = ''){
        Response::json();
        return ['code' => 200,'data'=> $data,'msg'=> $msg];
     }


    public function jsonError($msg = '',$code=-1,$data=[]){
        Response::json();
        return ['code' => $code,'data'=> $data,'msg'=> $msg];
    }
}