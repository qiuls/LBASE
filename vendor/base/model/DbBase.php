<?php
namespace Base\Model;

use Base\Traits\BaseModelTraits;
use Base\Traits\CURDTraits;
use PDO;
use Base\Config\Src\Config;
use Base\Facade\Log;
use ArrayAccess;
use \Exception;

class DbBase implements ArrayAccess
{

    use CURDTraits;
    use BaseModelTraits;


    public function __construct()
    {
        if (!self::getDb()) {
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
        if (!self::$config) {
            $config = config('database');
            $default = $config['default'];
            $default_config = $config['connections'][$default];
            self::$config = $default_config;
        }
        return self::$config;
    }

    /**
     * return $num
     * @param $sql
     * @return bool|int
     * @throws Exception
     */
    public static function execRow($sql)
    {
        try {
            $db = self::getDb();
            $count = $db->exec($sql);
            $code = $db->errorCode();
            if ($code == 00000) {
                return $count;
            } else {
                $error_arr = $db->errorInfo();
                $mesage = " mysql pdo error errorCode {$error_arr[0]} mysql error code {$error_arr[1]} message {$error_arr[2]}";
                throw  new \Exception($mesage);
            }
        } catch (\PDOException $e) {
            Log::message('PDO  execRow 错误' . $e->getMessage() . 'line ' . __LINE__ . 'file__' . __FILE__);
            throw  new \Exception('PDO execRow 错误' . $e->getMessage());
            return false;
        }
    }


    /**
     * return RowAll $arr
     * @param $sql
     * @return bool|int
     * @throws Exception
     */
    public static function execRowAll($sql)
    {

        try {

            $db = self::getDb();
            $res = $db->query($sql);
            $code = $db->errorCode();
            $data = [];
            if ($code == 00000) {
                while ($row_arr = $res->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = $row_arr;
                }
                return $data;
            } else {
                $error_arr = $db->errorInfo();
                $mesage = " mysql pdo error errorCode {$error_arr[0]} mysql error code {$error_arr[1]} message {$error_arr[2]}";
                throw  new \Exception($mesage);
            }
        } catch (\PDOException $e) {
            Log::message('PDO  execRow 错误' . $e->getMessage() . 'line ' . __LINE__ . 'file__' . __FILE__);
            throw  new \Exception('PDO execRow 错误' . $e->getMessage());
            return [];
        }
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
                static::$pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
                static::$pdo->exec("SET NAMES utf8mb4");
                return static::$pdo;
            } catch (\PDOException $e) {
                Log::message('PDO 连接错误' . $e->getMessage() . 'line ' . __LINE__ . 'file__' . __FILE__);
                throw  new \Exception('PDO 连接错误' . $e->getMessage());
            }
        }
        return static::$pdo;
    }
}

?>


                                                                                                     