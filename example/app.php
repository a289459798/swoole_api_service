<?php

/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/15
 * Time: 16:30
 */

$autoloader = require __DIR__ . '/../vendor/autoload.php';

$autoloader->addPsr4('Bijou\Example\\', __DIR__);
$app = new Bijou\App(
    [
        ['0.0.0.0', 9501],
        ['0.0.0.0', 9502, SWOOLE_SOCK_TCP],
    ]
);

$app->loadConfig(
    [
        'server' => [
            'worker_num' => 4,    //worker process num
            'backlog' => 128,   //listen backlog
            'max_request' => 500,
            'dispatch_mode' => 1
        ]
    ]
);

$app->loadRoute(
    [
        '/user' => [
            ['GET', '/{id:[0-9]+}', ['\Bijou\Example\User', 'getInfo']],
            ['GET', '/b', 'bbbbb'],
            ['POST', '/c', 'bbbbb'],
        ],

        ['GET', '/e', 'fffff'],
        ['POST', '/f', 'fffff'],
    ]
);
$app->run();