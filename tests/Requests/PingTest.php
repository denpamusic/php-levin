<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Requests\Ping;
use Denpa\Levin\Requests\RequestInterface;

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
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new Ping())->getCommandCode(), RequestInterface::P2P_COMMANDS_POOL_BASE + 3);
    }
}
