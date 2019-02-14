<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin;
use Denpa\Levin\Requests\Handshake;
use Denpa\Levin\Requests\RequestInterface;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\Uint32;
use Denpa\Levin\Types\Uint64;

class HandshakeTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new Handshake())->request());
        $this->assertInstanceOf(Uint64::class, (new Handshake())->request()['node_data']['local_time']);
        $this->assertEquals(new Uint32(0, Uint32::LE), (new Handshake())->request()['node_data']['my_port']);
        $this->assertEquals(Levin\peer_id(), (new Handshake())->request()['node_data']['peer_id']);
    }

    /**
     * @return void
     */
    public function testResponse() : void
    {
        $this->assertInstanceOf(Section::class, (new Handshake())->response());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new Handshake())->getCommandCode(), RequestInterface::P2P_COMMANDS_POOL_BASE + 1);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertEquals(0, (new Handshake())->my_port);
        $this->assertEquals(Levin\peer_id(), (new Handshake())->peer_id);
        $this->assertEquals(hex2bin('1230f171610441611731008216a1a110'), (new Handshake())->network_id);
        $this->assertEquals(hex2bin('418015bb9ae982a1975da7d79277c2705727a56894ba0fb246adaabb1f4632e3'), (new Handshake())->genesis);
    }
}
