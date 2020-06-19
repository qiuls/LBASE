<?php
/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/9
 * Time: 16:15
 */
namespace Base\Tool;
use Base\Config\Src\Config;
class Log {
    protected static $path;   //日志存储位置

    public function __construct()
    {

        $conf  = Config::base('app');
        self::$path = $conf['log'];
    }

    /**
     * 基于年月日 小时生成的日志
     * @param $message
     * @param string $file_ext
     * @return bool
     */
    public function message($message,$file_ext='log')
    {
        //1、确定文件存储位置是否存在 新建目录
        //2、写入日志
        if(!is_dir(self::$path.date('Ymd'))){
            mkdir(self::$path.date('Ymd'),'0777',true);
        }
        $file_name = self::$path.date('Ymd').DIRECTORY_SEPARATOR.date('H').'.'.$file_ext;
        $file = fopen($file_name,'a');
        $message = date('Y-m-d H:i:s').'  message:  '.$message.PHP_EOL;
        if(fwrite($file,$message))
        {
            return true;
        }
        return false;
    }

}