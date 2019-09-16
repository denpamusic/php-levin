<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin;
use Denpa\Levin\Requests\Handshake;

class HandshakeTest extends RequestTest
{
    /**
     * @var string
     */
    protected $classname = Handshake::class;

    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertRequestMap([
            'node_data'    => [
                'local_time' => Levin\uint64le(),
                'my_port'    => Levin\uint32le(),
                'network_id' => Levin\bytestring(),
                'peer_id'    => Levin\uint64le(),
            ],
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
            'node_data' => [
                'local_time' => Levin\uint64le(),
                'my_port'    => Levin\uint32le(),
                'network_id' => Levin\bytestring(),
                'peer_id'    => Levin\uint64le(),
            ],
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
        $this->assertCommandCode(1);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertVars([
            'my_port'               => 0,
            'peer_id'               => Levin\peer_id(),
            'network_id'            => hex2bin('1230f171610441611731008216a1a110'),
            'cumulative_difficulty' => 1,
            'current_height'        => 1,
            'top_version'           => 1,
            'top_id'                => hex2bin('418015bb9ae982a1975da7d79277c2705727a56894ba0fb246adaabb1f4632e3'),
            'peerlist'              => [],
        ]);
    }
}
