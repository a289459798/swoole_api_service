<?php
/**
 * Created by PhpStorm.
 * User: zhangzy
 * Date: 2017/4/20
 * Time: 15:01
 */

namespace Bijou\Tests;

$autoloader = require __DIR__ . '/../../vendor/autoload.php';
$autoloader->addPsr4('Bijou\Example\\', __DIR__ . '/../');

use Bijou\Example\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var User
     */
    private $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = new User();
    }

    public function testUser()
    {
        $data = $this->user->getInfo(1);
        $this->assertEmpty($data);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->user);
    }
}
