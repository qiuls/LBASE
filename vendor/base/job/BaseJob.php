<?php
namespace Base\Job;
use ArrayAccess;
use Base\Traits\ArrayObjectAccess;

class BaseJob implements ArrayAccess{
    use ArrayObjectAccess;

    protected $data = [];
    protected $timeOut = 3;
    public function setData($data){

        $this->data = $data;
        return $this;
    }
}
