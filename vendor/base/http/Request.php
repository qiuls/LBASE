<?php
namespace Base\Http;

use Base\Traits\RequestTraits;

class Request implements \ArrayAccess
{
    use RequestTraits;

    /**
     * 构造函数 合并请求对象
     * Request constructor.
     */
    public function __construct()
    {
        $this->post = $_POST;
        $this->get = $_GET;
        $this->attributes = array_merge($this->post,$this->get);
        $this->cookie = isset($_COOKIE) ? $_COOKIE : null;

    }

    public function post($key = null)
    {
        if ($key) {
            return isset($this->post[$key]) ? $this->post[$key] : null;
        }
        return $this->post;
    }

    public function get($key = null)
    {
        if ($key) {
            return isset($this->get[$key]) ? $this->get[$key] : null;
        }
        return $this->get;
    }

    public function all($key = null)
    {
        $data = array_merge($this->get(), $this->post());
        if ($key) {
            return $data[$key] ?: null;
        }
        return $data;
    }

    public function cookie($key = null)
    {
        if ($key) {
            return isset($this->cookie[$key]) ? $this->cookie[$key] : null;
        }
        return $this->cookie;
    }

    public function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getIp(){
              $ip = $_SERVER['REMOTE_ADDR'];
            if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
                foreach ($matches[0] AS $xip) {
                    if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                        $ip = $xip;
                        break;
                    }
                }
            }
            return $ip;
    }

}