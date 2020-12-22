<?php
namespace Base\Console;

use Base\Facade\Log;
use Base\Http\Request;
use Base\Queue\Execute;
use Base\Tool\PredisClient;
use Base\Model\DbBase;

class Queue extends BaseCommand
{

    protected $queue_execute = null;
    protected $redis_store = null;

    public function __construct(Execute $execute,PredisClient $redis_store)
    {
        $this->queue_execute = $execute;
        $this->redis_store = $redis_store;
    }

    public function handle()
    {
        $params = Request::console();
        dd($params);
    }

    public function init(){

        $tab_name = 'failed_jobs';
        $exist_sql = "drop table if exists $tab_name";
        DbBase::execRow($exist_sql);
        //创建error的表
        $str = "CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `param_data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        DbBase::execRow($str);
        Log::queueLog('初始化');
        dd('初始化');
    }

    public function work(){
        $this->queue_execute->work();

        Log::queueLog('执行成功');
        dd('执行成功');
    }

    public function listen(){
        Log::queueLog('start Queue');
        $this->queue_execute->handleListen();
        Log::queueLog('end Queue');

    }

    public function restart(){
        $params = Request::console();
        $restart_key = $this->queue_execute->getRestartKey();
        $this->redis_store->set($restart_key,time());
        Log::queueLog('restart Queue');
    }

    public function stop(){
        $params = Request::console();
        $stop_key = $this->queue_execute->getStopKey();
        $this->redis_store->set($stop_key,time());
    }


    /**
     * 错误队列推送重新执行
     */
    public function failWork(){
        Log::queueLog('start fail Queue');
        $this->queue_execute->failWork();
        Log::queueLog('end fail Queue');
    }


   

}