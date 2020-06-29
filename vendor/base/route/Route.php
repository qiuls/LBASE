<?php
namespace Base\Route;
class  Route
{
    protected $Url = null;
    protected $className = null;
    protected $methodName = null;
    protected static $baseController = 'App\\Front\\Controller\\';

    public static $post = [];
    public static $get = [];
    public static $put = [];
    public static $delete = [];
    public static $all = [];
    public static $group = [];

    public function __construct()
    {
        $url = $_SERVER['REQUEST_URI']; //URL
        $http_method = strtolower($_SERVER['REQUEST_METHOD']);
        $this->Url = $url;
        $key = $this->getUrlKey($url);

         //获取配置路由
        $validate_urls = $this->getValidateUrls($http_method);

        if (!array_key_exists($key, $validate_urls)) {
            throw new \Exception($this->Url.'路由不存在');
        }
        $class = $validate_urls[$key];
        if(is_object($class['method'])){
            $this->className = null;
            $this->methodName = $class['method'];
            return;
        }
        if (!is_array($class)) {
            $methods = explode('@', $class);
            $this->className = $methods[0];
            $this->methodName = $methods[1];
        }

        $methods = explode('@', $class['method']);
        $this->className = $methods[0];
        $this->methodName = $methods[1];
    }
    /**
     * 路由分组 分组参数 [namespace  prefix]
     * @param $param array
     * @param $closure
     */
    public static function group($param, $closure)
    {
        self::$group[] = $param;
        if (is_object($closure)) {
            $closure();
        }
        $group = self::$group;
        unset($group[count($group)-1]);
        self::$group = $group;
    }

    /**
     * post路由
     * @param $url
     * @param $method
     */
    public static function post($url, $method)
    {
        if (!self::$group) {
            self::$post[$url] = $method;
        }
        $data = self::getUrlData($method, $url);
        self::$post[$data['url']] = $data;
    }

    /**
     * put 路由
     * @param $url
     * @param $method
     */
    public static function put($url, $method)
    {
        if (!self::$group) {
            self::$put[$url] = $method;
        }
        $data = self::getUrlData($method, $url);
        self::$put[$data['url']] = $data;
    }

    /**
     * delete 路由
     * @param $url
     * @param $method
     */
    public static function del($url, $method)
    {
        if (!self::$group) {
            self::$delete[$url] = $method;
        }
        $data = self::getUrlData($method, $url);
        self::$delete[$data['url']] = $data;
    }

    /**
     * get 路由
     * @param $url
     * @param $method
     */
    public static function get($url, $method)
    {
        if (!self::$group) {
            self::$get[$url] = $method;
        }
        $data = self::getUrlData($method, $url);
        self::$get[$data['url']] = $data;
    }

    /**
     * 允许所有路由
     * @param $url
     * @param $method
     */
    public static function all($url, $method)
    {
        if (!self::$group) {
            self::$all[$url] = $method;
        }
        $data = self::getUrlData($method, $url);
        self::$all[$data['url']] = $data;
    }

    public function methodName($method)
    {
        $num = strpos($method, '?');
        if ($num) {
            return substr($method, 0, $num);
        }
        return $method;
    }

    /**
     * @param $http_method
     * @return array
     */
    protected function getValidateUrls($http_method)
    {
        switch ($http_method) {
            case 'post':
                $validate_urls = self::$post;
                break;
            case 'get':
                $validate_urls = self::$get;
                break;
            case 'put':
                $validate_urls = self::$put;
                break;
            case 'delete':
                $validate_urls = self::$delete;
                break;
            default:
                $validate_urls = self::$all;
                break;
        }

        $validate_urls = array_merge(self::$all,$validate_urls);

        return $validate_urls;
    }

    /**
     * @param $url
     * @return string
     */
    protected function getUrlKey($url)
    {
        if($url === '/')
        {
            return $url;
        }
        $urls = explode('/', $url);
        $url_count = count($urls);
        $end_key = $url_count - 1;
        if (isset($urls[1]) && $urls[1] == 'index.php') {
            unset($urls[1]);
            $urls[$end_key] = isset($urls[$end_key]) ? $this->methodName($urls[$end_key]) : '';
        } else {
            $urls[$end_key] = isset($urls[$end_key]) ? $this->methodName($urls[$end_key]) : '';
        }
        $key = substr(join('/', $urls), 1);
        return $key;
    }

    protected static function getUrlData($method, $url)
    {
        $group = self::getGroup();

        $data = [];
        $data['namespace'] = $group['namespace'];
        $data['prefix'] = $group['prefix'];
        $data['method'] = $method;

        if ($data['namespace']) {
            $data['method'] = $data['namespace'] . '\\' . $data['method'];
        }
        if (!is_object($data['method'])) {
            $data['method'] = self::$baseController . $data['method'];
        }
        if ($data['prefix']) {
            $url = $data['prefix'] . '/' . $url;
            $url = str_replace('\\','/',$url);
        }

        $data['url'] = $url;
        return $data;
    }

    protected static function getGroup()
    {
        $group = self::$group;
        $data['namespace'] = null;
        $data['prefix'] = null;
        if($group)
        {
            foreach($group as $value)
            {
                if(isset($value['namespace']))
                {
                    $data['namespace'][] = $value['namespace'];
                }
                if(isset($value['prefix']))
                {
                    $data['prefix'][] = $value['prefix'];
                }
            }
            $data['namespace'] = $data['namespace'] ? join('\\',$data['namespace']) : null;
            $data['prefix']    = $data['prefix'] ? join('\\',$data['prefix']) : null;
        }
        return $data;
    }

    /**
     * 实现方法访问属性
     * @param $name
     * @param $arguments
     * @return null
     */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'get') {
            $key = substr($name, 3);
            return isset($this->{$key}) ? $this->{$key} : null;
        }
        new \Exception('方法不存在');
    }
}