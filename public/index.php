<?php
/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/3
 * Time: 17:38
 */

include_once '../vendor/autoload.php';
include_once '../vendor/base/autolaod/autoload.php';

include_once '../vendor/base/funciotns/functions.php';
include_once '../vendor/base/app.php';
include_once '../route/Route.php';
define('APP_DEBUG',false); //是否开启debug
define('ROOT_DIR',dirname(__DIR__)); //root path
/**
 * 启动
 */
if(phpversion() <= '7.2'){
    die('php 版本太低');
}
global $App;
$App = new App();
$App->run();




