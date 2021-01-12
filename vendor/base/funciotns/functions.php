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


if (!function_exists('env')) {
    /**
     * 配置文件
     * @param  [type]  $dir  [description]
     * @param  integer $mode [description]
     * @return [type]        [description]
     */
    function env($key = null,$default = null)
    {
        $env_name = ROOT_DIR.'/.env';
        if(!is_file($env_name)){
            return null;
        }
        $env = parse_ini_file($env_name, true);    //解析env文件,name = PHP_KEY
        if($key && isset($env[$key])){
            return $env[$key] ?: null;
        }
        return $default;
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
        $config_name = null;
        if(strpos($keys,'.') !== false){
        list($config_name,$keys) = explode('.',$keys);
        }

        $app_config = \Base\Config\Src\Config::app(null);

        $base_config = Base\Config\Src\Config::base(null);

        foreach ($base_config as $key=> $item){
            if(isset($app_config[$key])){
                $app_config[$key] = array_merge($base_config[$key],$app_config[$key]);
                continue;
            }
            $app_config[$key] = $base_config[$key];
        }
        if($keys && $config_name){
            return $app_config[$config_name][$keys] ?? null;
        }
        if($keys) return $app_config[$keys] ?? null;
        
        return $app_config;
    }
}


if (!function_exists('from10_to62')) {

    /**
     * 十进制数转换成62进制
     *
     * @param integer $num
     * @return string
     */
    function from10_to62($num) {
        $to = 62;
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $ret = '';
        do {
            $ret = $dict[bcmod($num, $to)] . $ret;
            $num = bcdiv($num, $to);
        } while ($num > 0);
        return $ret;

    }

}

if (!function_exists('from62_to10')) {

    /**
     * 62进制数转换成十进制数
     *
     * @param string $num
     * @return string
     */
    function from62_to10($num) {
        $from = 62;
        $num = strval($num);
        $dict = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $len = strlen($num);
        $dec = 0;
        for($i = 0; $i < $len; $i++) {
            $pos = strpos($dict, $num[$i]);
            $dec = bcadd(bcmul(bcpow($from, $len - $i - 1), $pos), $dec);
        }
        return $dec;


    }
}

if(!function_exists('redirect')){

    function redirect($url)
    {
        header("Location: $url");
        exit();
    }
}

