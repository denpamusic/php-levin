<?php

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin\Notifications\NewFluffyBlock;
use Denpa\Levin\Notifications\NotificationInterface;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;

class NewFluffyBlockTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new NewFluffyBlock())->request());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new NewFluffyBlock())->getCommandCode(), NotificationInterface::BC_COMMANDS_POOL_BASE + 8);
    }
}
