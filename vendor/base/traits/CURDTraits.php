<?php
/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/5
 * Time: 16:00
 */
namespace Base\Traits;
trait CURDTraits
{
    public  $auto_update_time = true;
    /**
     * 获取where
     * @return array
     */
    public function getWhere($where= null)
    {
        $data =  [];
        $data['params'] = null;

        $where = $where ?: $this->where;
        $where_join_key = $this->where_join_key;
        $where_keys = $this->where_key;
        $keys = [];
        if(empty($where))
        {
            $data['sql'] = '';
            return $data;
        }
        foreach ($where as $key => $value) {
            if(is_array($value[0]))
            {
                $where_join_key_item = $where_join_key[$key];
                $son_where = self::getSonJoinSql($value,$where_join_key_item,$data);
                array_push($keys, $son_where);
                continue;
            }
            array_push($keys, "$value[0] $value[1] ?");
            $data['params'][]= $value[2];
        }


        $data['sql'] = self::getJoinSql($keys,$where_keys);

        return $data;
    }

    /**
     * 拼接where 子sql （id =1 or id=2 or id=3）
     */
     protected static function getSonJoinSql($where,$where_join_son,&$data)
     {
         $son_where =  '(';
         $item_i = count($where);
         $item_i--;
         foreach($where as $item_key => $item)
         {
             if($item_i == $item_key)
             {
                 $data['params'][]= $item[2];
                 $son_where .= " $item[0] $item[1] ?";
                 break;
             }
             else
             {
                 $data['params'][]= $item[2];
                 $son_where .= " $item[0] $item[1] ? {$where_join_son}";
             }

         }
         $son_where = $son_where.')';

         return $son_where;
     }

    /**
     * 拼接第一级 sql  （id =1 or id=2 or id=3） and name='200' or  name = '100'
     * @param $keys
     * @param $where_keys
     * @return string
     */
    protected static function getJoinSql($keys,$where_keys)
    {
        $sql ='';
//        $keys_item_i = count($keys);
//        $keys_item_i--;
//        var_dump($keys);
//        var_dump($where_keys);
//        die();
        foreach ($keys as $key_item => $key_val)
        {
            if(0 == $key_item)
            {
                $sql.=" {$key_val}";
                continue;
            } else {
                $key_item = $key_item -1;
                $sql.= ' '.$where_keys[$key_item]." {$key_val}";
            }
        }
        return $sql;
    }

    public function delSql($table)
    {
        $sql = "DELETE FROM $table";
        $data = $this->getSqlData($sql);
        return $data;
    }
    /**
     * 获取更新sql
     * @param $param
     * @param $table
     * @return mixed
     */
    public function updateSql($param,$table)
    {
        $sql = "update {$table} set ";
        $count = count($param);
        $count--;
        $param_value = array_values($param);
        $i = 0;
        foreach($param  as $key => $value)
        {
            if($i==$count)
            {
                $sql.="$key=?";
                break;
            }
            $sql.= "$key=?,";
            $i++;
        }
        $data = $this->getSqlData($sql);
        $data['params'] = array_merge($param_value,$data['params']);
        return $data;
    }


    /**
     * 获取添加sql
     */
    public function insertSql($param,$table)
     {
        $key = join(',',array_keys($param));
        $value = array_values($param);
        $tmp = null;
        $count = count($value);
        $count--;
         foreach($value as $item_k => $val)
         {
             if($item_k == $count)
             {
                 $tmp .= "?";
                 break;
             }
             $tmp .= "?,";
         }
         $sql = "INSERT INTO $table ($key) VALUES ($tmp)";
         $data['params'] = $value;
         $data['sql'] = $sql;
         return $data;
     }

    /**
     * 获取查询sql
     * @return mixed
     */
    public function getSelctSql()
    {
        $sql = "select {$this->select} from " . static::tableName();
        return $this->getSqlData($sql);
    }

    /**
     * 获取查询sql
     * @return mixed
     */
    public function getCountSelctSql()
    {
        $sql = "select count({$this->select}) from " . static::tableName();
        return $this->getSqlData($sql);
    }

    public function complexSql($order,$group,$offSet,$limit = null)
    {
        $data = $this->getSelctSql();

        if($group)
        {
            $data['sql'] .=" group by {$group}";
        }
        if($order)
        {
            $data['sql'] .=" order by $order";
        }
        if($limit)
        {
            $data['sql'] .=  " limit {$limit}";
        }
        if($offSet !== null)
        {
            $data['sql'] .= " offset {$offSet}";
        }
        return $data;
    }


    public function countComplexSql($order,$group){
        $data = $this->getCountSelctSql();
        if($group)
        {
            $data['sql'] .=" group by {$group}";
        }
        if($order)
        {
            $data['sql'] .=" order by $order";
        }
        return $data;
    }

    /**
     * where条件拼接
     * @param $sql
     * @return mixed
     */
    public function getSqlData($sql,$where =null)
    {
        $whereParam = $this->getWhere($where);

        $sqlData = $whereParam;
        if(strlen($sqlData['sql'])>1)
        {
            $sql .=   ' where ';
        }
        $sqlData['sql'] =   $sql.$sqlData['sql'];
        return $sqlData;
    }

}