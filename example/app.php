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
            'task_worker_num' => 8,
//            'daemonize' => true,
        ],
    ]
);

$app->addListener(['0.0.0.0', 9502, SWOOLE_TCP]);


$app->loadRoute(
    [
        '/user' => [
            ['POST', '/', ['\Bijou\Example\User', 'createUser']],
            ['GET', '/{id:[0-9]+}', ['\Bijou\Example\User', 'getUser']],
            ['PUT', '/{id:[0-9]+}', ['\Bijou\Example\User', 'updateUser']],
            ['DELETE', '/{id:[0-9]+}', ['\Bijou\Example\User', 'deleteUser']],
        ],

        ['GET', '/feed/{id:[0-9]+}', ['\Bijou\Example\Feed', 'getInfo'], 'cache' => true],
        ['POST', '/feed', ['\Bijou\Example\Feed', 'create'], 'security' => ['\Bijou\Example\Feed', 'check']],
        ['GET', '/feed/email', ['\Bijou\Example\Feed', 'postEmail']],
        ['GET', '/feed/service', ['\Bijou\Example\Feed', 'service']],
        ['GET', '/feed/user/{id:[0-9]+}', ['\Bijou\Example\Feed', 'getUser']],
        ['GET', '/export', ['\Bijou\Example\Export', 'getApi']],
        ['GET', '/pool/mysql', ['\Bijou\Example\Pool', 'mysql']],
        ['GET', '/curl/get', ['\Bijou\Example\Curl', 'get']],
        ['GET', '/curl/post', ['\Bijou\Example\Curl', 'post']],

        '/log' => [
            ['POST', '/', ['\Bijou\Example\Log', 'postLog']],
            ['GET', '/{id}', ['\Bijou\Example\Log', 'getLog']],
            ['GET', '/search/{keyword}', ['\Bijou\Example\Log', 'searchLog']],
        ],
    ]
);

$app->setCache(__DIR__ . '/cache', 3600, BIJOU_CACHE_FILE);

$app->setWebSocket('\Bijou\Example\Chat');

$app->addDecorator(new \Bijou\Example\Decorator\TimeDecorator());
$app->addDecorator(new \Bijou\Example\Decorator\ExceptionDecorator());

$app->addService(new \Bijou\Example\Service\TestService());

$app->addPool('mysql', new \Bijou\Example\Driver\Mysql());

$app->run();