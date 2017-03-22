# Bijou

基于swoole的高性能API框架，你完全不用学习swoole，就可以得到swoole的性能，支持异步任务（AsyncTask） 和 基于EventLop 的后台常驻服务（Service）等高级功能，支持api文档导出功能

### 使用

```php
$autoloader = require __DIR__ . '/../vendor/autoload.php';

$autoloader->addPsr4('Bijou\Example\\', __DIR__);

$app = new Bijou\App(['0.0.0.0', 9501]);

$app->run();
```

## 基础功能

### 服务器配置
与swoole完全一致，[https://wiki.swoole.com/wiki/page/274.html](https://wiki.swoole.com/wiki/page/274.html)

```php
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
```

### 多端口监听

```php
$app->addListener(['0.0.0.0', 9502, SWOOLE_TCP]);
```

### 路由

基于nikic/fast-route，输出方式可以直接`return`，也可以使用`response->send`，当使用`return`的时候，接口就是一个公共方法，可以很方便其他api调用

```php
$app->loadRoute(
    [
        '/user' => [
            ['GET', '/{id:[0-9]+}', ['\Bijou\Example\User', 'getInfo']],
            ['GET', '/b', 'bbbbb'],
            ['POST', '/', ['\Bijou\Example\User', 'create']],
        ],

        ['GET', '/feed/{id:[0-9]+}',  ['\Bijou\Example\Feed', 'getInfo']],
        ['POST', '/feed', ['\Bijou\Example\Feed', 'create']],
        ['GET', '/feed/email', ['\Bijou\Example\Feed', 'postEmail']],
        ['GET', '/feed/service', ['\Bijou\Example\Feed', 'service']],
    ]
);
```

路由回调，可以不需要继承任何class，get参数和post数据，通过方法参数传递

```php
class User
{
	// GET
    public function getInfo($id)
    {

        return json_encode([
            'id' => $id
        ]);
    }

	// POST
    public function create($body, $formData)
    {
        return json_encode([
            'body' => $body,
            'form' => $formData
        ]);
    }
}
```

如果需要获取request 和 response 相关，需要继承 `BijouApi`

```php
class Feed extends BijouApi
{

    public function getInfo($id)
    {

        $this->getResponse()->sent("12121212");

        return json_encode(
            ['id' => $id]
        );
    }

    public function create()
    {
        return json_encode([
            'post' => $this->getRequest()->post,
            'data' => $this->getRequest()->getBody(),
        ]);
    }

}
```

接口之间调用

```php
class User
{
    public function getInfo($id)
    {

        return json_encode([
            'id' => $id
        ]);
    }

    public function create($body, $formData)
    {
        return json_encode([
            'body' => $body,
            'form' => $formData
        ]);
    }
}

...

class Feed extends BijouApi
{

    public function getUser($id)
    {
        // 调用user api的方法
        return $this->invokeApi(['\Bijou\Example\User', 'getInfo'], [$id]);
    }
}
```

### api安全
可以在执行路由回调之前，注册一个handler，来做验证，比如auth或sign，回调函数必须返回true，否则会抛出403

```php
$app->setSecurityRoute([
    '/feed' => ['\Bijou\Example\Feed', 'check']
]);

...

public function check()
{
    return true;
}
```

## 高级功能

### 支持websocket

很简单的实现websocket的支持

```php
// 第二个参数true，表示支持websocket
$app = new Bijou\App(['0.0.0.0', 9501], true);

// 注册websocket的回调
$app->setWebSocket('\Bijou\Example\Chat');

...

class Chat
{
    public function onOpen(WebSocket $server, Request $request)
    {
        echo 'onOpen: 连接标识：' . $request->getClient();
    }

    public function onMessage(WebSocket $server, Frame $frame)
    {
        echo 'onMessage:' . $frame->getData();
        $server->send($frame->getClient(), json_encode([1, 2, 3, 4]));
    }

    public function onClose()
    {
        echo 'onClose';
    }
}

```

### 注册钩子

#### api请求

注册该钩子后，api请求完成之后，会回调，可以很方便统计一次完整的请求所需要记录的东西，需要继承 `RunTimeDecorator`

```php
$app->addDecorator(new \Bijou\Example\Decorator\TimeDecorator());

...

class TimeDecorator extends RunTimeDecorator
{

    public function setRunTime($time)
    {
        echo "api:" . $this->getRequest()->getApi() . "   运行时间:" . $time . '\r\n';
    }
}
```

#### 错误处理

该钩子可以记录api请求过程中，出现的所有错误，同时可以自定义http response 的错误处理（目前内置了一些默认的处理），需要继承 `ExceptionDecorator`

```php
$app->addDecorator(new \Bijou\Example\Decorator\ExceptionDecorator());

...

class ExceptionDecorator extends \Bijou\Decorator\ExceptionDecorator
{

    /**
     * @param \Throwable $throwable
     * @return Array
     */
    public function throwException(\Throwable $throwable)
    {
        echo 'file:' . $throwable->getMessage() . '--line:' . $throwable->getLine();
        return [
            'code' => '自定义提示代码/默认代码:' . $throwable->getCode(),
            'message' => '自定义提示错误信息/默认信息:' . $throwable->getMessage()
        ];
    }
}
```

### 常驻后台服务

支持通过静态注册常驻后台的service，可以添加多个，需要实现 `ServiceInterface` 接口

```php
// 注册service
$app->addService(new \Bijou\Example\Service\TestService());

...

class Feed extends BijouApi
{

    public function service()
    {
        // 开始执行，发送不同的action
        $this->startService('Bijou\Example\Service\TestService', 'action1', ['data' => 'data1']);
        $this->startService('Bijou\Example\Service\TestService', 'action2', ['data' => 'data2']);
        return '123';
    }

}

...

class TestService implements ServiceInterface
{
    /**
     * 同一个service，可以根据不同的action分别处理
     * @param $action
     * @param array $data
     * @return mixed
     */
    public function onCommand($action, Array $data)
    {
        switch ($action) {
            case 'action1':

                var_dump($data);
                break;
            case 'action2':
                var_dump($data);
                break;
        }
    }
}

```

### 异步任务

支持异步任务，用于处理一些耗时操作，比如发送推送、短信、邮件等，需要设置 `task_worker_num > 0`，异步任务需要实现 `AsyncTaskInterface` 接口

```php
class Feed extends BijouApi
{
    public function postEmail()
    {
    	// 执行一个异步任务
        $this->addAsyncTask(new EmailTask('zhangzy@bijou.com'));
        return '123';
    }

}

...

class EmailTask implements AsyncTaskInterface
{

    private $email;
    public function __construct($email)
    {
        $this->email = $email;
    }

    public function doInBackground($from_id)
    {
        sleep(10);
        var_dump('doInBackground');
    }

    public function onFinish()
    {
        var_dump('onFinish');
    }
}
```
#### 内置Api文档导出异步任务

```php
$this->exportApi(new ExportApi());
```

demo中有个例子`ExportApi`，如果要自定义实现 `ExportApiInterface`

```php
interface ExportApiInterface
{
    /**
     * @param array $apis
     * @return mixed
     */
    public function export(Array $apis);
}

```
api相关信息通过注解来声明

```php
/**
 * 获取发帖用户信息
 * @param $id
 * @return mixed
 */
public function getUser($id)
{

    return $this->invokeApi(['\Bijou\Example\User', 'getInfo'], [$id]);
}
```
对于不想导出的api，加 `@Ignore` 注解

```php
/**
 * @Ignore
 * @return bool
 */
public function check()
{
    return true;
}
```

### 对象池

适用于频繁使用的对象，目前只支持静态注册，目前处于测试阶段

```php
$app->addPool('mysql', new \Bijou\Example\Driver\Mysql());

```

需要实现`PoolInterface`接口

```php
class Mysql implements PoolInterface
{

    public function sleep($size)
    {
        for($i = 0; $i < $size; $i++) {

    }
        return 'mysql 测试连接池';
    }

    /**
     * 释放资源
     * @return mixed
     */
    public function release()
    {
    }

    /**
     * @return mixed
     */
    public function __clone()
    {
    }

    /**
     * 每个进程允许的最大空闲连接数
     * @return int
     */
    public function allowPoolSize()
    {
        return 20;
    }

    /**
     * 每个进程运行的最大连接数
     * @return int
     */
    public function maxPoolSize()
    {
        return 10;
    }
}
```

调用

```php
$this->pool('mysql')->sleep(1000000000);
```

### 开发者

zhangzy QQ:289459798