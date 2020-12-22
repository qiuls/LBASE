<?php

return [
    'base_queue' => [
        'queue:init' => \Base\Console\Queue::class,
        'queue:work' => \Base\Console\Queue::class,
        'queue:listen' => \Base\Console\Queue::class,
        'queue:restart' => \Base\Console\Queue::class,
        'queue:stop' => \Base\Console\Queue::class,
        'queue:failWork' => \Base\Console\Queue::class,
    ],

];
