<?php

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin\Notifications\NotificationInterface;
use Denpa\Levin\Notifications\RequestGetObjects;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;

class RequestGetObjectsTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new RequestGetObjects())->request());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new RequestGetObjects())->getCommandCode(), NotificationInterface::BC_COMMANDS_POOL_BASE + 3);
    }
}