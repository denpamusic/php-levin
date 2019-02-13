<?php

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin\Notifications\NewBlock;
use Denpa\Levin\Notifications\NotificationInterface;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;

class NewBlockTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new NewBlock())->request());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new NewBlock())->getCommandCode(), NotificationInterface::BC_COMMANDS_POOL_BASE + 1);
    }
}
