<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin;
use Denpa\Levin\Types\Uint64;
use Denpa\Levin\Requests\Ping;
use Denpa\Levin\Requests\RequestInterface;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;

class PingTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new Ping())->request());
    }

    /**
     * @return void
     */
    public function testResponse() : void
    {
        $this->assertInstanceOf(Section::class, (new Ping())->response());
        $this->assertInstanceOf(Uint64::class, (new Ping())->response()['my_id']);
        $this->assertEquals(Levin\peer_id(), (new Ping())->response()['my_id']);
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new Ping())->getCommandCode(), RequestInterface::P2P_COMMANDS_POOL_BASE + 3);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertEquals(Levin\peer_id(), (new Ping())->my_id);
    }
}
