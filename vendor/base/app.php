<?php

class App
{
    public $config = null;

    /**
     * 获取配置文件
     */
    public function config($key = null)
    {
        return config($key);
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
        //自定义时间
        ini_set('date.timezone', config('app.app_timezone'));

        error_reporting(0);
        register_shutdown_function("cache_shutdown_error");

        try {
            $route = self::getRoute();

            $middleware = $route->getmiddleware();

            //处理中间件
            if ($middleware) {
                $middleware = array_unique($middleware);
                foreach ($middleware as $value) {
                    self::handleMiddleware($value);
                }
            }

            $className = $route->getclassName();

            $methodName = $route->getmethodName();

            $res = $this->response($className, $methodName);

            exit($res);
        } catch (\Exception $e) {
            $this->error_class($e);
        }
    }


    /**
     * 命令行启动
     * @param $param
     * ^ array:3 [
     * 0 => "artisan.php"
     * 1 => "handle_for_joke"
     * 2 => "a:1"
     * ]
     */
    public function runConsole($param)
    {
        //自定义时间
        ini_set('date.timezone', config('app.app_timezone'));
        error_reporting(0);

        register_shutdown_function("cache_shutdown_error");
        try {
            unset($param[0]);
            if (count($param) <= 0) {
                dd('参数错误');
            }
            //获取配置的handle名
            $app_handle =   config('console.handle');
            $app_handle = $app_handle ?: [];
            $base_handle = config('console.base_queue') ?? [];

            $handle_names = array_merge($app_handle,$base_handle);
            if (!$handle_names) {
                dd('console 配置文件不存在');
            }
            //处理路由
            $command = $param[1];
            $handle_name = $handle_names[$command] ?? '';
            if (!$handle_name) {
                dd('console handle as name 不存在');
            }
            unset($param[1]);

            //获取参数
            $data_param = [];
            if ($param) {
                foreach ($param as $item) {
                    //参数为key:value形式
                    $line_param = explode(':', $item);
                    if (!$line_param && count($line_param) < 2) {
                        continue;
                    }
                    list($key, $val) = $line_param;
                    $data_param[$key] = $val;
                }
            }
            \Base\Http\Request::setConsole($data_param);
            if(isset($base_handle[$command])){
                list($class_name,$method)= explode(':',$command);
                return Ioc::make($handle_name, $method);
            }
            return Ioc::make($handle_name, 'handle');
        } catch (\Exception $e) {
            $this->error_class($e);
        }
    }


    /**
     * 执行web中间件
     * @param $className
     * @param string $methodName
     * @return bool
     */
    public function handleMiddleware($className, $methodName = 'handle')
    {
        $res = Ioc::make($className, $methodName);
        if ($res === true) {
            return true;
        }
        if (\Base\Http\Response::$returnType === 'json') {
            echo json_encode($res);
            exit();
        } elseif (\Base\Http\Response::$returnType === 'html') {
            echo $res;
            exit();
        }
    }

    /**
     * 返回格式
     * @param $className
     * @param $methodName
     */
    public function response($className, $methodName)
    {
        //对闭包函数进行处理
        if ($className === null && is_object($methodName)) {
            $methodName();
            return null;
        }
        $res = Ioc::make($className, $methodName);
        if (\Base\Http\Response::$returnType === 'json') {
            return json_encode($res);

        } elseif (\Base\Http\Response::$returnType === 'html') {
            return $res;
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
            if (!method_exists('Ioc', $name)) {
                $message = 'You can\'t access undefined methods to class ' . __CLASS__  .' name = '.$name;
                if(method_exists($this,$name)){
                     return $this->$name(...$arguments);
                }
                throw new \Exception($message);
            }

            return Ioc::$name(...$arguments);
        } catch (\Exception $e) {
            $this->error_class($e);
        }
    }

    public function make($className, $methodName, $params = []){
        return Ioc::make($className, $methodName, $params);
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
        $this->errorMessage($message, $file, $line);
    }


    /**
     * @param $message
     * @param $file
     * @param $line
     */
    public function errorMessage($message, $file, $line)
    {
        if (config('app.env') == 'live') {
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

