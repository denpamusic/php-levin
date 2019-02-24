<?php

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin;
use Denpa\Levin\Notifications\RequestFluffyMissingTx;

class RequestFluffyMissingTxTest extends NotificationTest
{
    /**
     * @var string
     */
    protected $classname = RequestFluffyMissingTx::class;

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
        $this->assertCommandCode(9);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertVars();
    }
}
