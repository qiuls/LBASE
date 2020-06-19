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

