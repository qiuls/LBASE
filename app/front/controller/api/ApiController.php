<?php

namespace App\Front\Controller\api;
use Base\Controller\BaseController;
use App\Front\Model\RequestLog;
use Base\Http\Request;
use Base\Facade\Log;
use Base\Http\Response;
use Base\Route\Route;

class ApiController extends BaseController
{

    public function log(Request $request)
    {

       $url = $request->post('url');
       $type = $request->post('type');

//       var_dump(RequestLog::$typeAll);
//       var_dump($type);
//       var_dump(in_array($type,RequestLog::$typeAll));
       if(!in_array($type,RequestLog::$typeAll)){
           return $this->jsonError('类型错误');
       }
        $ip = $request->getIp();
        $date = date('Y-m-d');
        $s_date = $date . ' 00:00:00';
        $e_date = $date . ' 23:59:59';
        $query = RequestLog::query();
        $data =$query->where('created_at',$s_date,'>=')->where('created_at',$e_date,'<=')->findOne();
        if($data){
            return $this->jsonSuccess('200');
        }
        $model = new RequestLog();
        $model->url = $url;
        $model->type = $type;
        $model->ip = $ip;
        $res= $model->save();
        if(!$res){
            return $this->jsonError('保存失败');
        }
        return $this->jsonSuccess();
    }


}