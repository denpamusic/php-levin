<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin;
use Denpa\Levin\Requests\StatInfo;

class StatInfoTest extends RequestTest
{
    /**
     * @var string
     */
    protected $classname = StatInfo::class;

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
        $this->assertResponseMap();
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertCommandCode(4);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertVars();
    }
}
