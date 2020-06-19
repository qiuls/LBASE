<?php
/**
 * 路由分组 支持命名空间 和 prefix url
 */
use Base\Route\Route;

Route::get('/','IndexController@index');

Route::group(['prefix' => 'index'],function()
{
    Route::get('route','IndexController@route');
    Route::get('rely','IndexController@rely');
});

Route::group(['namespace' => 'V1'],function()
{
    Route::group(['namespace' => 'Api'],function()
    {
        Route::get('route','IndexController@route');
        Route::get('rely','IndexController@rely');
    });

});

Route::group(['prefix' => 'api'],function()
{
    Route::post('log','ApiController@log');
});

