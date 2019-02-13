<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin\Requests\Handshake;
use Denpa\Levin\Requests\RequestInterface;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\Uint64;

class HandshakeTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $handshake = new Handshake();
        $this->assertInstanceOf(Section::class, $handshake->request());
        $this->assertInstanceOf(Uint64::class, $handshake->request()['node_data']['local_time']);
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
        $handshake = new Handshake();
        $this->assertEquals($handshake->network_id, hex2bin('1230f171610441611731008216a1a110'));
        $this->assertEquals($handshake->genesis, hex2bin('418015bb9ae982a1975da7d79277c2705727a56894ba0fb246adaabb1f4632e3'));
    }
}
