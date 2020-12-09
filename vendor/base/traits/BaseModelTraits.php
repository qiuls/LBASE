<?php
/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/3
 * Time: 16:51
 */
namespace Base\Traits;
use Ioc;

trait BaseModelTraits
{
    use ArrayObjectAccess;
    protected static $table = '';
    protected static $self = null;
    protected static $pdo;
    protected static $config;
    protected $select = '*';
    protected $where = null;
    protected $where_key = [];
    protected $where_join_key = [];
    protected $group = null;
    protected $orderBy = null;
    protected $offSet = null;
    protected $limit  = 0;
    protected $count   = null;
    protected $exec_sql = null;
    protected $primaryKey = 'id';


    /**
     * 设置查询属性
     * @param $name
     * @param $value
     * @return $this
     */
    public function setAttributes($name,$value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * 获取查询数据
     * @return null
     */
    public function toArray()
    {
        $data = $this->attributes;
        if(isset($data[0]) && is_object($data[0])){
            foreach ($data as &$v){
                $v = $v->toArray();
            }
        }
        return $data;
    }

    /**
     * 访问不存在的静态和无权限的方法时候调用
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name,$arguments)
    {
        $ob = self::query();

        if (!method_exists($ob, $name)) {
            $message = 'You can\'t access undefined methods to class ' . __CLASS__.' function:'.$name;
            throw new \Exception($message);
        }

        return $ob->$name(...$arguments);
    }

    /**
     * 访问不存在的无权限的方法时候调用
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {

           if (!method_exists($this, $name)) {
                $message = 'You can\'t access undefined methods to class ' . __CLASS__ .' function:'.$name;
                throw new \Exception($message);
            }
            return $this->$name(...$arguments);

    }


}