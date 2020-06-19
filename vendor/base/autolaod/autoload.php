<?php

class autoLoads
{
 protected static $loads = null;

public  static function getLoads()
 {
     /* 解析类名为文件路径 */
     if(self::$loads)
     {
         return self::$loads;
     }
     $base = dirname(__DIR__).DIRECTORY_SEPARATOR;
     $app = dirname(dirname(dirname(__DIR__))).DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR;
     self::$loads = $loads = [
         'base' =>  $base,
         'app' =>  $app,
     ];
     return self::$loads;
 }

    public  static function loader($class)
    {
        $namespaces = explode('\\',$class);

        $namespaces = self::topStrtolower($namespaces);

        self::getTop($class,$namespaces);

        $class = str_replace('\\',DIRECTORY_SEPARATOR,$class).'.php';

        if(!file_exists($class))
        {
            throw new Exception($class.'文件不存在');
        }
        include_once  $class;
    }

    /**
     * 命名空间转换小写 除了最后文件名
     * @param $namespaces
     */
    public static function topStrtolower($namespaces)
   {
     $count =count($namespaces);
     $count--;

     foreach($namespaces as $key => $value)
     {
         if($key == $count)
         {
            break;
         }

         $namespaces[$key] = strtolower($value);
     }
       return $namespaces;

   }
    /**
     * 替换顶级命名空间路径
     * @param $namespaces
     */
    public static function getTop(&$class,$namespaces)
    {
        $vendor_key = $namespaces[0];
        $loads = self::$loads ?: self::getLoads();
        if(isset($loads[$vendor_key]))
        {
            unset($namespaces[0]);
            $str = join('\\',$namespaces);
            $class = $loads[$vendor_key].$str;
        }
    }

    /**
     * 加载控制器
     * @param $class
     * @param $namespaces
     */
    public static function loadController(&$namespaces)
    {
        if(isset($namespaces[2]) && $namespaces[2] == 'controller')
       {
           $str = ucfirst($namespaces[3]);
           $namespaces[3] =  isset($namespaces[3]) ? $str.'Controller' : null;
       }

    }
    /**
     * 加载数据模型
     * @param $class
     * @param $namespaces
     */
    public static function loadModel(&$namespaces)
    {
        if(isset($namespaces[2]) && $namespaces[2] =='model')
        {
            $str = ucfirst($namespaces[3]);
            $namespaces[3] =  isset($namespaces[3]) ? $str.'Model' : '';
        }
    }
}
spl_autoload_register(array('autoLoads','loader'));

