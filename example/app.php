<?php

/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/3/15
 * Time: 16:30
 */

$autoloader = require __DIR__ . '/../vendor/autoload.php';

$autoloader->addPsr4('Bijou\Example\\', __DIR__);

$app = new Bijou\App(['0.0.0.0', 9501], true);

$app->loadConfig(
    [
        'server' => [
            'worker_num' => 4,    //worker process num
            'backlog' => 128,   //listen backlog
            'max_request' => 500,
            'dispatch_mode' => 1,
            'task_worker_num' => 8
        ]
    ]
);

$app->addListener(['0.0.0.0', 9502, SWOOLE_TCP]);


$app->loadRoute(
    [
        '/user' => [
            ['GET', '/{id:[0-9]+}', ['\Bijou\Example\User', 'getInfo']],
            ['POST', '/', ['\Bijou\Example\User', 'create']],
        ],

        ['GET', '/feed/{id:[0-9]+}',  ['\Bijou\Example\Feed', 'getInfo']],
        ['POST', '/feed', ['\Bijou\Example\Feed', 'create']],
        ['GET', '/feed/email', ['\Bijou\Example\Feed', 'postEmail']],
        ['GET', '/feed/service', ['\Bijou\Example\Feed', 'service']],
        ['GET', '/feed/user/{id:[0-9]+}', ['\Bijou\Example\Feed', 'getUser']],
        ['GET', '/export', ['\Bijou\Example\Export', 'getApi']],
    ]
);

$app->setSecurityRoute([
    '/feed' => ['\Bijou\Example\Feed', 'check']
]);

$app->setWebSocket('\Bijou\Example\Chat');

$app->addDecorator(new \Bijou\Example\Decorator\TimeDecorator());
$app->addDecorator(new \Bijou\Example\Decorator\ExceptionDecorator());


$app->addService(new \Bijou\Example\Service\TestService());

$app->run();