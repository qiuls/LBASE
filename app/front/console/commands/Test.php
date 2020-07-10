<?php


namespace App\Front\Console\Commands;


use App\Front\Console\Command;

class Test extends Command
{

    public function handle()
    {
       dd('test');
    }
}