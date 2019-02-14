<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin;
use Denpa\Levin\Requests\PeerId;
use Denpa\Levin\Requests\RequestInterface;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\uInt64;

class PeerIdTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new PeerId())->request());
    }

    /**
     * @return void
     */
    public function testResponse() : void
    {
        $this->assertInstanceOf(Section::class, (new PeerId())->response());
        $this->assertInstanceOf(Uint64::class, (new PeerId())->response()['my_id']);
        $this->assertEquals(Levin\peer_id(), (new PeerId())->response()['my_id']);
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new PeerId())->getCommandCode(), RequestInterface::P2P_COMMANDS_POOL_BASE + 6);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertEquals(Levin\peer_id(), (new PeerId())->my_id);
    }
}
