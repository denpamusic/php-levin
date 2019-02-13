<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Requests\TimedSync;
use Denpa\Levin\Requests\RequestInterface;

class TimedSyncTest extends TestCase
{
    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Section::class, (new TimedSync())->request());
    }

    /**
     * @return void
     */
    public function testResponse() : void
    {
        $this->assertInstanceOf(Section::class, (new TimedSync())->response());
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertEquals((new TimedSync())->getCommandCode(), RequestInterface::P2P_COMMANDS_POOL_BASE + 2);
    }
}
