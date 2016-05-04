<?php

return [
    'debug'     => env('SWOOLE_DEBUG', false),
    'pid_path'  => storage_path(),
    'servers'   => [
        'default'   => [
            'server_type'       => 'HttpServer',
            'ip'                => '0.0.0.0',
            'port'              => '8898',
            'task_worker_num'   => 2,
            'worker_num'        => 3,
            'daemonize'         => true,
            'max_request'       => 10000,
        ],
    ],
];
