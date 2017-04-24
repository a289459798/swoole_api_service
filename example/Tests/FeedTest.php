<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/4/24
 * Time: 14:56
 */

namespace Bijou\Tests;

$autoloader = require __DIR__ . '/../../vendor/autoload.php';
$autoloader->addPsr4('Bijou\Example\\', __DIR__ . '/../');

use Bijou\Example\Feed;
use Bijou\Http\Response;

class FeedTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Feed
     */
    private $feed;

    public function setUp()
    {
        parent::setUp();
        $this->feed = new Feed(null, null, new Response(new \Swoole\Http\Response(), null));
    }

    public function testGetInfo()
    {
        $data = $this->feed->getInfo(123);
        var_dump($data);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->feed);
    }
}
