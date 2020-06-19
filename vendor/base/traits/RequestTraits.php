<?php
/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/6
 * Time: 17:36
 */
namespace Base\Traits;
trait RequestTraits
{
 use ArrayObjectAccess;
 protected $get = [];
 protected $post = [];
 protected $seesion = null;
 protected $cookie = null;

}