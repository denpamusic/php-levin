<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin\Requests\NetworkState;

class NetworkStateTest extends RequestTest
{
    /**
     * @var string
     */
    protected $classname = NetworkState::class;

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
        $this->assertCommandCode(5);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertVars();
    }
}
