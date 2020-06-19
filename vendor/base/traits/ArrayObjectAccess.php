<?php
/**
 * Created by PhpStorm.
 * User: leilay
 * Date: 2018/1/9
 * Time: 11:34
 */
namespace Base\Traits;
trait ArrayObjectAccess
{
    protected $attributes = null;

    /**
     * array 数组访问形式
     * @param $offset
     * @param $value
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->attributes[] = $value;
        } else {
            $this->attributes[$offset] = $value;
        }
    }

    public function offsetExists($offset) {
        return isset($this->attributes[$offset]);
    }

    public function offsetUnset($offset) {
        unset($this->attributes[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->attributes[$offset]) ? $this->attributes[$offset] : null;
    }

    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name,$value);
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    public function __unset($name)
    {
        $this->offsetUnset($name);
    }
}