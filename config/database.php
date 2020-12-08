<?php
/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/4
 * Time: 10:13
 */
return [
    'default' => env('DB_CONNECTION'),

    'connections' => [
        'mysql' => [
               'driver' => 'mysql',
               'host' => env('DB_HOST'),
               'port' => env('DB_PORT'),
               'database' =>env('DB_DATABASE'),
               'username' => env('DB_USERNAME'),
               'password' => env('DB_PASSWORD'),
               'unix_socket' =>  '',
               'charset' => 'utf8mb4',
               'collation' => 'utf8mb4_unicode_ci',
               'prefix' => '',
               'strict' => false,
               'engine' => null,
           ],
    ],
    'redis' => [

        'client' => 'predis',

           'default' => [
               'host' => '127.0.0.1',
               'password' =>  null,
               'port' => 6379,
               'database' => 0,
           ],

       ],

];