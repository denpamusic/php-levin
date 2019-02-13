<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Requests\SupportFlags;
use Denpa\Levin\Requests\RequestInterface;

class SupportFlagsTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new SupportFlags())->request());
    }

    /**
     * @return void
     */
    public function testResponse() : void
    {
        $supportflags = new SupportFlags();
        $this->assertInstanceOf(Section::class, $supportflags->response());
        $this->assertEquals(1, $supportflags->response()['support_flags']->toInt());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new SupportFlags())->getCommandCode(), RequestInterface::P2P_COMMANDS_POOL_BASE + 7);
    }
}
