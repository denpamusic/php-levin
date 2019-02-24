<?php

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin;
use Denpa\Levin\Notifications\RequestGetObjects;

class RequestGetObjectsTest extends NotificationTest
{
    /**
     * @var string
     */
    protected $classname = RequestGetObjects::class;

    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertRequestMap();
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertCommandCode(3);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertVars();
    }
}
