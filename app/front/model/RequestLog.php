<?php
namespace App\Front\Model;
use Base\Model\Model;
class RequestLog extends Model
{

  protected static $table = 'request_log';

  //类型 浏览 提交
  const TYPE_1 = 'view';

    static $typeAll = [
      self::TYPE_1,
  ];
}

