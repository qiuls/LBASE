<?php
namespace Base\Http;
class Response
{
    const RETURN_TYPE1 = 'json';
    const RETURN_TYPE2 = 'html';

 public static $returnType = '';

 public static function json(){
     header('content-type:application/json;charset=utf-8');
     self::$returnType = self::RETURN_TYPE1;
 }

}