<?php

namespace App\Front\Console\Commands;

use App\Front\Console\Command;
use App\Front\Model\Joke;
use App\Front\Service\ElasticsearchService;

class ForJokeToEs extends Command
{

    /**
     * 负责处理定时任务
     */
   public function handle()
   {
       $client = new ElasticsearchService();
       $index_name = 'jokes';
       if (!$client->checkIndexExists($index_name)) {
           $client->indexAdd($index_name);
           $client->typeAdd($index_name, [
               'id' => [
                   'type' => 'integer'
               ],
               'content' => [
                   'type' => 'text',
                   "analyzer" => "ik",
               ],
               'passtime' => [
                   'type' => 'date',
               ],
               'images' => [
                   'type' => 'text'
               ],
               'title' => [
                   'type' => 'text'
               ]
           ]);
       }
       $jokes = Joke::query()->findAll();
       $jokes = $jokes->toArray();
       $max_joke =  $jokes[count($jokes) -1];

       try{
           if($client->get($index_name,$max_joke['id'])){
            dd('已添加');
           }
       }catch (\Exception $exception){
           $res = json_decode($exception->getMessage(),true);
           if(isset($res['found']) && $res['found'] == false){
           } else{
               dd($exception->getMessage());
           }
       }

       foreach ($jokes as $value) {
           $es_row = [
               'id' => $value['id'],
               'content' => $value['content'],
               'passtime' => $value['passtime'],
               'images' => $value['images'],
               'title' => $value['title'],
           ];
           $client->indexTypeAddRow('jokes', $es_row,$value['id']);
       }
   }

}