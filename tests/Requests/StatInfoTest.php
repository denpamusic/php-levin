<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin\Requests\RequestInterface;
use Denpa\Levin\Requests\StatInfo;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;

class StatInfoTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new StatInfo())->request());
    }

    /**
     * @return void
     */
    public function testResponse() : void
    {
        $this->assertInstanceOf(Section::class, (new StatInfo())->response());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new StatInfo())->getCommandCode(), RequestInterface::P2P_COMMANDS_POOL_BASE + 4);
    }
}
