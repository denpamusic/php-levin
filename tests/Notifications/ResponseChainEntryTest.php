<?php

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Notifications\ResponseChainEntry;
use Denpa\Levin\Notifications\NotificationInterface;

class ResponseChainEntryTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new ResponseChainEntry())->request());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new ResponseChainEntry())->getCommandCode(), NotificationInterface::BC_COMMANDS_POOL_BASE + 7);
    }
}
