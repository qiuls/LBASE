<?php
/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/9
 * Time: 15:53
 */
namespace Base\Facade;
use Exception;
abstract  class Facade
{
    protected static function getFacadeClassName()
    {
        throw new Exception('Facade does not implement class.');
    }

    public static function __callStatic($name, $arguments)
    {
        $class_name = static::getFacadeClassName();
        if(!method_exists($class_name,$name))
        {
            throw new Exception("class {$class_name} not function $name");
        }
        return  lapp()->make($class_name,$name,$arguments);
    }
}