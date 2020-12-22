<?php
namespace Base\Queue;

use Base\Tool\PredisClient;

class Push{


    protected  $redis_store = null;
    protected $rpush_key = 'LBASE_RPUSH_LIST_';

    protected $queue_key = 'LBASE_QUEUE_KEY_';

    protected $error = 3;

    public function __construct(PredisClient $redis_store)
    {
        $this->redis_store = $redis_store;
    }

    public function getRedisStore(){
        return $this->redis_store;
    }

    public function getErrorNum(){
        return $this->error;
    }

    public function getQueueKey($def_name = 'default'){
        return  $hash_key = $this->rpush_key.$def_name;;
    }

    public function getQueueHashKey($def_name = 'default'){
        return  $hash_key = $this->queue_key.$def_name;;
    }

    public function push($class,$param,$def_name = 'default'){
        $data = [
            'id' => '',
            'class' => $class,
            'param' => $param,
            'error' => 0,
        ];

        $id = date('YmdHis').mt_rand(1,9999);
        $from62_id = $this->getForm62PushId();
        $push_key = $this->getQueueKey($def_name);
        $hash_key = $this->getQueueHashKey($def_name);

        $exists_hkey = $this->redis_store->hexists($hash_key,$from62_id);
        if($exists_hkey){
            $from62_id = $this->getForm62PushId();
        }
        $data['id'] = $from62_id;

        $res = $this->redis_store->rpush($push_key,$from62_id);
        if(!$res){
            return false;
        }

        $res = $this->redis_store->hset($hash_key,$from62_id,json_encode($data));
        if(!$res){
            return false;
        }
        return $from62_id;
    }


    public function getForm62PushId(){
        $id = date('YmdHis').mt_rand(1,9999);
        $from62_id = from10_to62($id);
        return $from62_id;
    }






}