<?php

/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/9
 * Time: 16:37
 */

namespace App\Front\middleware;

use Base\Http\Request;
use Base\Http\Response;
use Base\Middleware\BaseMiddleware;
use Base\Tool\Jwt;

class CheckWebToken extends BaseMiddleware
{
   public function handle(Request $request,Response $response){
       $token = $request->cookie('token');
       if(!$token){
           return $this->jsonError('非法操作');
       }
       if(!Jwt::verifyToken($token)){
           return $this->jsonError('非法操作1');
       }
       return true;
   }
}