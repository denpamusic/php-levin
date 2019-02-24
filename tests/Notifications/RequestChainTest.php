<?php

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin;
use Denpa\Levin\Notifications\RequestChain;

class RequestChainTest extends NotificationTest
{
    /**
     * @var string
     */
    protected $classname = RequestChain::class;

    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertRequestMap([
            'block_ids' => Levin\bytestring(),
        ]);
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertCommandCode(6);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertVars([
            'block_ids' => '',
        ]);
    }
}
