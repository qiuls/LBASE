## [wiki](https://github.com/qiuls/LBASE/wiki "wiki")

##有问题反馈
在使用中有任何问题，欢迎反馈给我，可以用以下联系方式跟我交流

* 邮件(qlsindex@139.com)
* QQ: 1805668790
*在兴趣的驱动下,写一个`免费`的东西，有欣喜，也还有汗水，希望你喜欢我的作品，同时也能支持一下。
*本框架专为api设计还未兼容web 只有MC 没有视图层，使用市场上大部分开源框架加载文件都太多，太重，不适合api开发，放到github上面也是为了避免大家重复造轮子，有机会可以帮助一起完善谢谢

## 关于作者 QC

粗略记录当前框架使用，后续慢慢更新
##
```
 app  --系统文件
  -front
  --controller  
  --facade  
  --middleware  
  --model  
  --service
 composer.json  
 composer.lock  
 config 配置文件
 -app.php
 -database.php  
 public  公共文件
  -index.php
 route  路由
  -Route.php 
 storage 
  -image 上传的图片
  -log  日志
  -sql 上线的sql
 vendor 系统文件
  -autoload.php 系统自动加载 
  -base 框架核心
```
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
 前面使用了分组后续不需要分组这样些 最开始的group 或者post|get加/就不会使用上面的group 
 Route::get('/route','IndexController@route');
 
 Route::group(['namespace' => '/V1'],function(){
  
  });
  ```
## 中间件
```
    继承 use Base\Middleware\BaseMiddleware;
    class CheckWebToken extends BaseMiddleware
    {
       public function handle(Request $request,Response $response){
          return true;
       }
    }
    
    路由使用 
    Route::group(['prefix' => 'test','namespace' => 'test','middleware'=> \App\Front\middleware\CheckWebToken::class],function()
    {
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

cookie获取
public function index(Request $request)
{
         var_dump($request->cookie());
}
session获取
public function index(Request $request)
{
             var_dump($request->session());
}
 $request->all();获取所有
 ```
 
##数据模型操作
       
 ```
  默认必须指定created_at updated_at 默认数据类型 timestamp Null
  public $auto_update_time = false;// 关闭 数据模型添加此属性
 
       更新
       $model = new User();
       $model->url = 'qls11';
       $model->save(1) 更新主键为1的数据; 
       $model = User::where('id',1)->findOne();
       $model->url = 'qls11';$model->save() 更新主键为1的数据
       
        添加
        $model = new User();
        $model->url = 'qls11';
        $model->save();
       
       查询
       User::where('id',1)->findOne();
       $model = User::where([['id','=',1],['id','=',2]],'or')->findAll();
       
       删除
        model = User::where('id',1)->findOne(); 
        $model->delete();
              
       //多条件and查询
       $query = RequestLog::query();
       $data =$query->where('created_at',$s_date,'>=')->where('created_at',$e_date,'<=')->findOne();
       
       查询多行
        $query = RequestLog::query();
        $data =$query->where('created_at',$s_date,'>=')->where('created_at',$e_date,'<=')->findAll();
        
       统计查询 count 或者先where 然后在count 在查询数据 findOne or findAll
       
        $count = $query->where('created_at',$s_date,'>=')->where('created_at',$e_date,'<=')->count();      
       
       获取执行的sql
       $model::query();
       $model->getSql();
       更多语法待更新
 ```
  ##调试 dd命令
  ```
 $arr = '12121'; //OR 数组 OR 对象 
 使用 dd();
  ```
## [sql美化](https://www.jianshu.com/p/e3ada9f1b6cci)
```
 $query = "SELECT * FROM `contacts` LIMIT 0, 1000";
 echo SqlFormatter::format($query);
```

 ##新增predis 链接
 
   ```
    'redis' => [
           'client' => 'predis',
           'default' => [
                  'host' => '127.0.0.1',
                  'password' =>  null,
                  'port' => 6379,
                  'database' => 0,
              ],
              ],
              
      使用
     Predis::keys()        
   ```
   ###新增env方法
    ```
    在根目录新建.env文件
    
    APP_ENV=local
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=json
    DB_USERNAME=root
    DB_PASSWORD=root
    系统调用
    env('APP_ENV') 不存在返回false 存在为local
     ```