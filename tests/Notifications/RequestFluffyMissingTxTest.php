<?php

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Notifications\RequestFluffyMissingTx;
use Denpa\Levin\Notifications\NotificationInterface;

class RequestFluffyMissingTxTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new RequestFluffyMissingTx())->request());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new RequestFluffyMissingTx())->getCommandCode(), NotificationInterface::BC_COMMANDS_POOL_BASE + 9);
    }
}
