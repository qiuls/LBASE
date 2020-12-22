<?php

namespace  App\Front\Job;
use Base\Job\BaseJob;
use \Base\Facade\Log;
class TestJob extends BaseJob
{



    public function __construct()
    {

    }


    /**
     * 默认处理方法
     */
    public function handle(){
       Log::message(__METHOD__ .json_encode($this->data));
       sleep(5);
        return true;
    }
}