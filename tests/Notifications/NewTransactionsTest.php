<?php

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin\Notifications\NewTransactions;
use Denpa\Levin\Notifications\NotificationInterface;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;

class NewTransactionsTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new NewTransactions())->request());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new NewTransactions())->getCommandCode(), NotificationInterface::BC_COMMANDS_POOL_BASE + 2);
    }
}
