<?php

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Notifications\RequestChain;
use Denpa\Levin\Notifications\NotificationInterface;

class RequestChainTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new RequestChain())->request());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new RequestChain())->getCommandCode(), NotificationInterface::BC_COMMANDS_POOL_BASE + 6);
    }
}
