<?php

/**
 * 该文件只作为编辑器解析 不作为代码使用
 */
namespace Base\Facade {


    class Log {

        public static function message($message,$is_date = true,$file_ext ='log')
        {
            /** @var Base\Tool\Log $instance */
            return $instance->message($message,$is_date,$file_ext);
        }

        public static function queueLog($message,$is_date)
        {
            /** @var Base\Tool\Log $instance */
            return $instance->queueLog($message,$is_date);
        }

    }

    class QPush{
        public static function getErrorNum()
        {
            /** @var Base\Queue\Push $instance */
            return $instance->getErrorNum();
        }


        public static function getQueueKey($def_name)
        {
            /** @var Base\Queue\Push $instance */
            return $instance->getQueueKey($def_name);
        }


        public static function push($class_name,$param,$def_name = 'default')
        {
            /** @var Base\Queue\Push $instance */
            return $instance->push($class_name,$param,$def_name);
        }

    }


}