<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin;
use Denpa\Levin\Requests\TimedSync;

class TimedSyncTest extends RequestTest
{
    /**
     * @var string
     */
    protected $classname = TimedSync::class;

    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertRequestMap([
            'payload_data' => [
                'cumulative_difficulty' => Levin\uint64le(),
                'current_height'        => Levin\uint64le(),
                'top_id'                => Levin\bytestring(),
                'top_version'           => Levin\uint8(),
            ],
        ]);
    }

    /**
     * @return void
     */
    public function testResponse() : void
    {
        $this->assertResponseMap([
            'local_time'   => Levin\uint64le(),
            'payload_data' => [
                'cumulative_difficulty' => Levin\uint64le(),
                'current_height'        => Levin\uint64le(),
                'top_id'                => Levin\bytestring(),
                'top_version'           => Levin\uint8(),
            ],
            'local_peerlist_new' => Levin\bytearray(),
            'local_peerlist'     => Levin\bytestring(),
        ]);
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertCommandCode(2);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertVars([
            'cumulative_difficulty' => 1,
            'current_height'        => 1,
            'top_version'           => 1,
            'top_id'                => hex2bin('418015bb9ae982a1975da7d79277c2705727a56894ba0fb246adaabb1f4632e3'),
            'peerlist'              => [],
        ]);
    }
}
