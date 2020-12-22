<?php
namespace Base\Model\Queue;
use Base\Model\BaseModel;

class FailedJob extends BaseModel{

    protected static $table = 'failed_jobs';


    public $auto_update_time = true;

}

?>
