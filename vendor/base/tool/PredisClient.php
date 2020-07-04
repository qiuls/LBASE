<?php


namespace Base\Tool;


use Predis\Client;

class PredisClient
{

 // https://www.jianshu.com/p/2acbeea668ef 文档

    protected $redis = null;
    public function __construct()
    {
        $redis_conf = config('database.redis');
        $this->redis = $redis = new Client($redis_conf);
    }

    public function set($key,$value,$second = 0){
        if($second > 0){
          return  $this->redis->setex($key,$second,$value);
        }
        return $this->redis->set($key,$value);
    }

    public function get($key){
        return $this->redis->get($key);
    }


    /**
     * 递增
     */
    public function incr($key){
        return $this->redis->incr($key);
    }

    /**
     * 递减
     */
    public function decr($key){
        return $this->redis->decr($key);
    }

    /**
     * 递减
     */
    public function del($key){
        return $this->redis->del($key);
    }

    public function keys($key = '*'){
        return $this->redis->keys($key);
    }

    public function ttl($key){
        return $this->redis->ttl($key);
    }

    public function expire($key,$second = 0){
        if($second){
            return $this->redis->expire($key,$second);
        }
        return $this->redis->expire($key);
    }

    public function __call($name, $arguments)
    {
        try{
            return $this->redis->$name($arguments);
        }catch (\Exception $e){
            lapp()->error_class($e);
            return false;
        }
    }
}