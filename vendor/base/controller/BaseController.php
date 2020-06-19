<?php
/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/3
 * Time: 16:33
 */
namespace Base\Controller;

use Base\Http\Response;

class BaseController
{

    public function jsonSuccess($data=[],$msg = ''){
         Response::json();
         return ['code' => 200,'data'=> $data,'msg'=> $msg];
    }


    public function jsonError($msg = '',$code=-1,$data=[]){
        Response::json();
        return ['code' => $code,'data'=> $data,'msg'=> $msg];
    }
}