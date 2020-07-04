<?php
if (!function_exists('mkdirs')) {
    /**
     * 判断文件夹是否存在不存在则创建
     * @param  [type]  $dir  [description]
     * @param  integer $mode [description]
     * @return [type]        [description]
     */
    function mkdirs($dir, $mode = 0755)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) {
            return true;
        }
        if (!mkdirs(dirname($dir), $mode)) {
            return false;
        }
        return @mkdir($dir, $mode);
    }
}
if (!function_exists('lapp')) {
    /**
     * 获取app实例
     */
    function lapp()
    {
       return $GLOBALS['App'];
    }
}

if(!function_exists('cache_shutdown_error'))
{
    function cache_shutdown_error() {
        $_error = error_get_last();
        if ($_error && in_array($_error['type'], array(1, 4, 16, 64, 256, 4096, E_ALL))) {
            $message = '你的代码出错了'.$_error['message'];
            $file = $_error['file'];
            $line = $_error['line'];
            lapp()->errorMessage($message,$file,$line);
        }
    }
}


if(!function_exists('base_load'))
{
    function base_load($path) {
        if(file_exists($path)){
            include_once $path;
        }
        $base_path = dirname(dirname(\Base\Config\Src\Config::getBaseDir()));
        $path =  $base_path .DIRECTORY_SEPARATOR.$path;
        include_once $path;
    }
}


if (!function_exists('config')) {
    /**
     * 配置文件
     * @param  [type]  $dir  [description]
     * @param  integer $mode [description]
     * @return [type]        [description]
     */
    function config($keys = null)
    {
        $key = null;
        if(strpos($keys,'.') !== false){
        list($config_name,$key) = explode('.',$keys);
        }else{
            $config_name = $keys;
        }
        $app_config = \Base\Config\Src\Config::app($config_name);
        if($app_config){
           if($key) return $app_config[$key] ?? '';
           if($config_name) return $app_config ?? '';
       }
       $base_config = Base\Config\Src\Config::base($config_name);
        if($base_config && $key){
            if($key) return $base_config[$key] ?? '';
            if($base_config) return $base_config ?? '';
        }

        return array_merge($base_config,$app_config);
    }
}