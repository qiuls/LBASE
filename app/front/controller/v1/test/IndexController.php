<?php

namespace App\Front\Controller\V1\Test;
use Base\Controller\BaseController;
use App\Front\Model\RequestLog;
use Base\Http\Request;
use Base\Facade\Log;
use Base\Route\Route;

class IndexController extends BaseController
{
    /**
     * 依赖注入
     * @param Request $request
     */
  public function rely(Request $request)
  {
      print_r($request->all());
      print_r(lapp()->make('Base\Http\Request','get'));
  }

  public function index(Request $request)
  {
      Log::message('我是lbase');
//     // var_dump($request->get());
//     // require 'a.php';
//    $model = User::where([['id','=',1],['id','=',2]],'or')->findAll();
//    var_dump($model);
      /**
       * update $model = new User();$model->url = 'qls11';$model->save(1) 更新主键为1的数据;
       * || $model = User::where('id',1)->findOne();$model->url = 'qls11';$model->save() 更新主键为1的数据;
       * add $model = new User();$model->url = 'qls11';$model->save();
       * select User::where('id',1)->findOne();
       * delete  暂无
       */
  }

    public function route()
    {

        print_r(Route::$get);

    }

}