<?php

class App
{
    public $config  = null;

    /**
     * 获取配置文件
     */
     public function config($key = null)
     {
         if($this->config)
         {
             $config = $this->config;
         }
         else
         {
             $config = Base\Config\Src\Config::base();
             $this->config = $config;
         }
         if(!$key)
         {
             return $config;
         }
         return isset($config[$key]) ? $config[$key] : null;
     }

    /**
     * 获取路由
     * @return Route
     */
    public static function getRoute()
    {
        $Route = new Base\Route\Route();
        return $Route;
    }

    /**
     * 系统启动
     * @return mixed
     */
    public function run()
    {
        ini_set('date.timezone',$this->config('app_timezone'));
        error_reporting(0);
        register_shutdown_function("cache_shutdown_error");
        try {
            $route = self::getRoute();

            $className = $route->getclassName();
            $methodName = $route->getmethodName();


           return $this->response($className,$methodName);

        } catch (\Exception $e) {
              $this->error_class($e);
        }
    }

    public function response($className,$methodName){
        //对闭包函数进行处理
        if($className ===null && is_object($methodName)){
            $methodName();
            return;
        }
        $res = Ioc::make($className, $methodName);
        if(\Base\Http\Response::$returnType === 'json'){
            echo json_encode($res);
            exit();
        }elseif(\Base\Http\Response::$returnType === 'html'){
            echo $res;
            exit();
        }
        return $res;
    }

    /**
     * 访问不存在的无权限的方法时候调用
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        try {
            if (!method_exists('Ioc', $name))
            {
                $message = 'You can\'t access undefined methods to class ' . __CLASS__;

                throw new \Exception($message);
            }
            return Ioc::$name(...$arguments);
        }
        catch (\Exception $e)
        {
            $this->error_class($e);
        }
    }

    /**
     * 错误日志记录
     * @param $e
     */
    protected function error_class($e)
    {
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $this->errorMessage($message,$file,$line);
    }


    /**
     * @param $message
     * @param $file
     * @param $line
     */
    public function errorMessage($message,$file,$line)
    {
        if (APP_DEBUG) {
            $str = <<<HTML
  message : {$message}\r\n
  file : {$file}\r\n
  line: {$line}\r\n
HTML;
            \Base\Facade\Log::message($str);
            exit();
        }
        $str = <<<HTML
  message : {$message}</br>
  file : {$file}</br>
  line: {$line}</br>
HTML;
        echo $str;
        exit();
    }

}


/**
 * 依赖注入类
 * Class Ioc
 */
class Ioc
{
    protected static $Instance = null;

    // 获得类的对象实例
    public static function getInstance($className, $params = [])
    {
        $paramArr = self::getMethodParams($className);
        $instance = (new ReflectionClass($className))->newInstanceArgs(array_merge($paramArr, $params));
        self::$Instance[$className] = $instance;
        return $instance;
    }

    /**
     * 执行类的方法
     * @param  [type] $className  [类名]
     * @param  [type] $methodName [方法名称]
     * @param  [type] $params     [额外的参数]
     * @return [type]             [description]
     */
    public static function make($className, $methodName, $params = [])
    {
        // 获取类的实例
        if (!isset(self::$Instance[$className])) {
            $instance = self::getInstance($className);
        } else {
            $instance = self::$Instance[$className];
        }
        // 获取该方法所需要依赖注入的参数
        $paramArr = self::getMethodParams($className, $methodName);
        $param = array_merge($paramArr, $params);
        return $instance->{$methodName}(...$param);
    }

    /**
     * 获得类的方法参数，只获得有类型的参数
     * @param  [type] $className   [description]
     * @param  [type] $methodsName [description]
     * @return [type]              [description]
     */
    protected static function getMethodParams($className, $methodsName = '__construct')
    {
        // 通过反射获得该类
        $class = new ReflectionClass($className);
        $paramArr = []; // 记录参数，和参数类型
        // 判断该类是否有构造函数
        if ($class->hasMethod($methodsName)) {
            // 获得构造函数
            $construct = $class->getMethod($methodsName);
            // 判断构造函数是否有参数
            $params = $construct->getParameters();
            if (count($params) > 0) {
                // 判断参数类型
                foreach ($params as $key => $param) {
                    if ($paramClass = $param->getClass()) {
                        // 获得参数类型名称
                        $paramClassName = $paramClass->getName();//string(1) "C"
                        // 获得参数类型
                        $args = self::getMethodParams($paramClassName);
                        $paramArr[] = (new ReflectionClass($paramClass->getName()))->newInstanceArgs($args);
                    }
                }
            }
        }
        return $paramArr;
    }
}

