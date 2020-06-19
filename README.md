## [wiki](https://github.com/qiuls/LBASE/wiki "wiki")

##有问题反馈
在使用中有任何问题，欢迎反馈给我，可以用以下联系方式跟我交流

* 邮件(qlsindex@139.com)
* QQ: 1805668790
*在兴趣的驱动下,写一个`免费`的东西，有欣喜，也还有汗水，希望你喜欢我的作品，同时也能支持一下。
*本框架专为api设计还未兼容web，使用市场上大部分开源框架加载文件都太多，太重，不适合api开发，放到github上面也是为了避免大家重复造轮子，有机会可以帮助一起完善谢谢

## 关于作者 qls

粗略记录当前框架使用，后续慢慢更新
 ###路由
 ```
 <?php
 /**
  * 路由分组 支持命名空间 和 prefix url 支持post get put delete
  */
 use Base\Route\Route;
 
 Route::get('/','IndexController@index');
 
 Route::group(['prefix' => 'index'],function()
 {
     Route::get('route','IndexController@route');
     Route::get('rely','IndexController@rely');
 });
 
 
 Route::group(['namespace' => 'V1'],function()
 {
     Route::group(['namespace' => 'Test'],function()
     {
         Route::get('route','IndexController@route');
         Route::get('rely','IndexController@rely');
     });
 
 });
  ```
## 控制器
 ```
get 获取
public function index(Request $request)
  {
      var_dump($request->get());
  } 
 post获取
 
 public function index(Request $request)
   {
       var_dump($request->post());
  }
  
  $request->all();获取所有
 ```          
 ##数据模型操作
       
 ```
       更新
       * $model = new User();$model->url = 'qls11';$model->save(1) 更新主键为1的数据; 
       * $model = User::where('id',1)->findOne();$model->url = 'qls11';$model->save() 更新主键为1的数据
       
        添加
       * $model = new User();$model->url = 'qls11';$model->save();
       
       查询
       * User::where('id',1)->findOne();
       $model = User::where([['id','=',1],['id','=',2]],'or')->findAll();
       
       删除
       * model = User::where('id',1)->findOne(); $model->delete();
              
       //多条件and查询
       $query = RequestLog::query();
       $data =$query->where('created_at',$s_date,'>=')->where('created_at',$e_date,'<=')->findOne();
       
       获取执行的sql
       $model::query();
       $model->getSql();
       更多语法待更新
 ```
 