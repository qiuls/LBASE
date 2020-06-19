<?php
/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/9
 * Time: 16:37
 */
namespace Base\Facade;
use Base\Tool\Log as LogMessage;
class Log extends Facade
{
 public static function getFacadeClassName()
 {
     return LogMessage::class;
 }
}