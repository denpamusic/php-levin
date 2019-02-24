<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin;
use Denpa\Levin\Requests\Ping;

class PingTest extends RequestTest
{
    /**
     * @var string
     */
    protected $classname = Ping::class;

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
            'status'  => Levin\bytestring(),
            'peer_id' => Levin\uint64le(),
        ]);
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertCommandCode(3);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertVars([
            'peer_id' => Levin\peer_id(),
        ]);
    }
}
