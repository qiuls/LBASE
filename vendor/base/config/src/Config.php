<?php

/**
 * 获取配置文件 app优先级大于系统配置
 * Class Config
 */
namespace Base\Config\Src;
class Config
{

    protected static $base_config = [];
    protected static $app_config = [];
    public static function getBaseDir()
    {
        $bass_config_dir = dirname(__DIR__);
        return $bass_config_dir;
    }


    public static function getAppDir()
    {
        $bass_config_dir = ROOT_DIR;
        return $bass_config_dir;
    }

    /**
     * 获取系统配置文件
     * @return string
     */
    public static function base($file_name = null)
    {
        $config = [];

        $base_dir = self::getBaseDir();
        if(!$file_name)
        {
            if(self::$app_config)
            {
                return self::$app_config;
            }

            $files = scandir($base_dir);
            $config = [];
            foreach($files as $value)
            {
                if($value == '.' || $value == '..')
                {
                    continue;
                }
                $file = $base_dir.DIRECTORY_SEPARATOR.$value;
                if(is_dir($file))
                {
                    continue;
                }
                $config_c = include_once $file;
                $config = array_merge($config,$config_c);
                unset($config_c);
            }
            self::$app_config = $config;
            return $config;
        }

        $file = $base_dir.DIRECTORY_SEPARATOR.$file_name.'.php';
        if(!file_exists($file))
        {
            throw  new \Exception("{$file}配置文件不存在");
        }
        $config = include $file;
        return $config;
    }
    /**
     * 获取app配置文件
     * @return string
     */
    public static function app($file_name)
    {

        $app_dir = self::getAppDir();

        $app_dir = $app_dir .DIRECTORY_SEPARATOR.'config';
        if(!$file_name)
        {
            if(self::$base_config)
            {
                return self::$base_config;
            }

            $files = scandir($app_dir);
            $config = [];
            foreach($files as $value)
            {
                if($value == '.' || $value == '..')
                {
                    continue;
                }
                $file = $app_dir.DIRECTORY_SEPARATOR.$value;
                if(is_dir($file))
                {
                    continue;
                }
                $config_c = include_once $file;
                $config = array_merge($config,$config_c);
                unset($config_c);
            }
            self::$base_config = $config;
            return $config;
        }
        $file = $app_dir.DIRECTORY_SEPARATOR.$file_name.'.php';
        if(!file_exists($file))
        {
            throw  new \Exception("{$file}配置文件不存在");
        }
        $config = include $file;
        return $config;
    }



}