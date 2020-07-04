<?php


namespace Base\Facade;


use Base\Tool\PredisClient;
use Predis\Client;

class Predis extends Facade
{

    protected static function getFacadeClassName()
    {
        return PredisClient::class;
    }
}