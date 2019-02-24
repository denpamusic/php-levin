<?php

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin;
use Denpa\Levin\Notifications\NewTransactions;

class NewTransactionsTest extends NotificationTest
{
    /**
     * @var string
     */
    protected $classname = NewTransactions::class;

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
        $this->assertCommandCode(2);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertVars();
    }
}
