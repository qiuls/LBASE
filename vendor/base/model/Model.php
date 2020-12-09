<?php
/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/3
 * Time: 16:35
 */
namespace Base\Model;
use Base\Config\Src\Config;
use Base\Facade\Log;
use Base\Traits\BaseModelTraits;
use PDO;
use ArrayAccess;
use Base\Traits\CURDTraits;
use \Exception;
class Model implements ArrayAccess
{

    use BaseModelTraits;
    use CURDTraits;

    public static function query()
    {
        if(!self::$self)
        {
            self::$self = new static();
        }
        return self::$self;
    }

    public function __construct()
    {
        if(!self::getDb()){
            $message = 'get db 链接失败 ' . __CLASS__;
            throw new \Exception($message);
        }
    }

    /**
     * 获取数据库配置文件
     * @return string
     */
    public static function getConfig()
    {
        if(!self::$config)
        {
            $config = config('database');
            $default = $config['default'];
            $default_config = $config['connections'][$default];
            self::$config = $default_config;
        }
        return self::$config;
    }
    /**
     * Get pdo instance
     * @return PDO
     */
    public static function getDb()
    {
        if (empty(static::$pdo)) {
            $config = self::getConfig();
            $host = $config['host'];
            $database = $config['database'];
            $username = $config['username'];
            $password = $config['password'];
            try {
                static::$pdo = new PDO("mysql:host=$host;dbname=$database",$username,$password);
                static::$pdo->exec("set names 'utf8'");
                return static::$pdo;
            }catch (\PDOException $e){
                Log::message('PDO 连接错误'.$e->getMessage() .'line '. __LINE__. 'file__'.__FILE__);
                throw  new \Exception('PDO 连接错误'.$e->getMessage());
            }
        }
        return static::$pdo;
    }
    /**
     * 获取数据表name
     * @return string the table name
     */
    public static function tableName()
    {
        if(static::$table)
        {
            return  static::$table;
        }
        return __CLASS__;
    }

    /**
     *  select拼接field
     * @param $str
     */
    protected function select($str='*')
    {
        $this->select = $str;
        return $this;
    }

    protected function orderBy($orderBy='id asc')
    {
        $this->orderBy = $orderBy;
        return $this;
    }
    protected function group($group=null)
    {
        $this->group = $group;
        return $this;
    }

    protected function offSet($offSet=null)
    {
        $this->offSet = $offSet;
        return $this;
    }

    protected function limit($limit = null)
    {
        $this->limit = $limit;
        return $this;
    }
    /**
     * where 条件拼接
     * @param $name
     * @param null $value
     * @param string $compare
     * @return $this
     * @throws \Exception
     */
    protected function where($name,$value=null,$compare='=')
    {
        if(!empty($this->where)){
            $this->where_key[] = 'and';
        }
        if(is_array($name))
        {
            $this->where_join_key[] = $value ?: 'and';
            $this->where[] = $name;
        }
        else
        {
            if($value === null)
            {
                throw  new  \Exception(__METHOD__.' value not empty');
            }
            $this->where[] = [$name,$compare,$value];
        }
        return $this;
    }


    protected function orWhere($name,$value=null,$compare='=')
    {
        if(is_array($name))
        {
            $this->where_join_key[] = $value ?: 'and';
            $this->where[] = $name;
        }
        else
        {
            if($value === null)
            {
                throw new   \Exception(__METHOD__.' value not empty');
            }
            $this->where[] = [$name,$compare,$value];
        }
        $this->where_key[] ='or';
        return $this;
    }

    /**
     * Model::where(条件)->findOne()
     * @return BaseModel|null
     */
    protected  function findOne($primaryValue = null)
    {
        if($primaryValue)
        {
            $this->where($this->primaryKey,$primaryValue);
        }

        $sqlData = $this->complexSql($this->orderBy,$this->group,$this->offSet);
        $sqlData['sql'] .= ' limit 1';
        $this->exec_sql($sqlData);
        $this->unsetWhereCondition();
        $stmt = static::getDb()->prepare($sqlData['sql']);
        $rs = $stmt->execute($sqlData['params']);

        if ($rs) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!empty($row)) {
                foreach ($row as $rowKey => $rowValue) {
                    $this->setAttributes($rowKey,$rowValue);
                }
                return $this;
            }
        }
        return null;
    }

    protected  function findAll()
    {

        $sqlData = $this->complexSql($this->orderBy,$this->group,$this->offSet,$this->limit);
        $this->exec_sql($sqlData);
        $this->unsetWhereCondition();
        $stmt = static::getDb()->prepare($sqlData['sql']);
        $rs = $stmt->execute($sqlData['params']);
        $data = [];
        if ($rs) {
            $all = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($all as $value){
                $model = new self();
                $model->attributes = $value;
                $data[] = $model;
            }
            $this->attributes = $data;
            return $this;
        }
        return false;
    }

    protected function unsetWhereCondition()
    {
        $this->select = '*';
        $this->group = null;
        $this->offSet = null;
        $this->orderBy = null;
        $this->limit = 0;
        $this->where = null;
        $this->where_join_key = null;
        $this->where_key = null;
    }

    /**
     * ~~~
     * Customer::delete();
     * ~~~
     */
    protected  function delete($primaryValue = null)
    {
        $this->findOne($primaryValue);

        if(!$this->attributes)
        {
            return null;
        }

        $sqlData = $this->delSql(static::tableName());
        $this->exec_sql($sqlData);
        $this->where = null;
        $res =  static::execute($sqlData);
        if(!$res)
        {
            return null;
        }

        if(isset($this->attributes[$this->primaryKey]))
        {
            unset($this->attributes[$this->primaryKey]);
        }

        $this->where = null;
        return true;
    }
    /**
     * ```php
     * $customer = new Customer;
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->save();
     * ```
     * 更新
     * $customer = Customer::findOne($primaryValue)
     * $customer->name = $name;
     * $customer->email = $email;
     * $customer->save();
     * @return boolean whether the model is inserted successfully.
     */
    protected function save($primaryValue = null)
    {
        if(!$this->attributes)
        {
            return null;
        }
        //更新
        if(isset($this->attributes[$this->primaryKey]) || $primaryValue)
        {
            if($this->auto_update_time){
                $this->updated_at = date('Y-m-d H:i:s');
            }
            $primaryValue = isset($this->attributes[$this->primaryKey]) ?: $primaryValue;
            $this->where($this->primaryKey,$primaryValue);
            $param = $this->attributes;
            unset($param[$this->primaryKey]);
            $sqlData = $this->updateSql($param,static::tableName());
            $this->exec_sql($sqlData);
            $this->unsetWhereCondition();
            $res = static::execute($sqlData);
            if(!$res)
            {
                return null;
            }
            return $this;
        }
        //添加

        if($this->auto_update_time){
            $this->created_at = date('Y-m-d H:i:s');
            $this->updated_at = date('Y-m-d H:i:s');
        }

        $sqlData = $this->insertSql($this->attributes,static::tableName());
        $this->exec_sql($sqlData);
        $this->unsetWhereCondition();
        $res = static::execute($sqlData);
        if(!$res)
        {
            return null;
        }
        $id = static::getDb()->lastInsertId();
        $this->setAttributes('id',$id);
        return $this;
    }

    protected static function execute($sqlData)
    {
        $stmt = static::getDb()->prepare($sqlData['sql']);
        $res = $stmt->execute($sqlData['params']);

        if ($stmt->errorCode() != '00000'){
            $db_error = $stmt->errorInfo();
            $db_error_str = "code $db_error[1] message：$db_error[2]";
            throw new \Exception($db_error_str);
        }
        return $res;
    }



    public  function exec_sql($sqlData)
    {
        $this->exec_sql[]= $sqlData;
    }



    /**
     * 返回执行的sql
     * @return |null
     */
    public function getSql(){
        return $this->exec_sql;
    }
}