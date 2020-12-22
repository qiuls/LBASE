<?php


namespace Base\Facade;

use \Base\Queue\Push;
class QPush extends Facade
{

    protected static function getFacadeClassName()
    {
        return Push::class;
    }
}