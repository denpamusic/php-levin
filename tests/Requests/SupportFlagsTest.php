<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin;
use Denpa\Levin\Requests\SupportFlags;

class SupportFlagsTest extends RequestTest
{
    /**
     * @var string
     */
    protected $classname = SupportFlags::class;

    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertRequestMap();
    }

    /**
     * @return void
     */
    public function testResponse() : void
    {
        $this->assertResponseMap([
            'support_flags' => Levin\uint32le(),
        ]);
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertCommandCode(7);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertVars([
            'support_flags' => SupportFlags::P2P_SUPPORT_FLAG_FLUFFY_BLOCKS,
        ]);
    }
}
