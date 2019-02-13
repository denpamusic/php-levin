<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin\Requests\NetworkState;
use Denpa\Levin\Requests\RequestInterface;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;

class NetworkStateTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new NetworkState())->request());
    }

    /**
     * @return void
     */
    public function testResponse() : void
    {
        $this->assertInstanceOf(Section::class, (new NetworkState())->response());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new NetworkState())->getCommandCode(), RequestInterface::P2P_COMMANDS_POOL_BASE + 5);
    }
}
