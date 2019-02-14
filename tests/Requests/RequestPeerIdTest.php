<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin;
use Denpa\Levin\Requests\RequestInterface;
use Denpa\Levin\Requests\RequestPeerId;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\uInt64;

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
        $this->assertInstanceOf(Uint64::class, (new RequestPeerId())->response()['my_id']);
        $this->assertEquals(Levin\peer_id(), (new RequestPeerId())->response()['my_id']);
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new RequestPeerId())->getCommandCode(), RequestInterface::P2P_COMMANDS_POOL_BASE + 6);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertEquals(Levin\peer_id(), (new RequestPeerId())->my_id);
    }
}
