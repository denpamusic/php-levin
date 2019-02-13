<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Requests\RequestPeerId;
use Denpa\Levin\Requests\RequestInterface;

class RequestPeerIdTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new RequestPeerId())->request());
    }

    /**
     * @return void
     */
    public function testResponse() : void
    {
        $this->assertInstanceOf(Section::class, (new RequestPeerId())->response());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new RequestPeerId())->getCommandCode(), RequestInterface::P2P_COMMANDS_POOL_BASE + 6);
    }
}
