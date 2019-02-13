<?php

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin\Notifications\NotificationInterface;
use Denpa\Levin\Notifications\ResponseGetObjects;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;

class ResponseGetObjectsTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new ResponseGetObjects())->request());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new ResponseGetObjects())->getCommandCode(), NotificationInterface::BC_COMMANDS_POOL_BASE + 4);
    }
}
