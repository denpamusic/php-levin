<?php

namespace Denpa\Levin\Requests;

use Denpa\Levin;
use Denpa\Levin\Command;
use Denpa\Levin\Section\Section;

class Handshake extends Command implements RequestInterface
{
    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function request() : Section
    {
        return new Section([
            'node_data' => new Section([
                'local_time' => Levin\uint64le(time()),
                'my_port'    => Levin\uint32le($this->my_port),
                'network_id' => Levin\bytestring($this->network_id),
                'peer_id'    => $this->peer_id,
            ]),
            'payload_data' => new Section([
                'cumulative_difficulty' => Levin\uint64le(1),
                'current_height'        => Levin\uint64le(1),
                'top_id'                => Levin\bytestring($this->genesis),
                'top_version'           => Levin\ubyte(1),
            ]),
        ]);
    }

    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function response() : Section
    {
        return new Section();
    }

    /**
     * @return array
     */
    protected function defaultVars() : array
    {
        return [
            'my_port'    => 0,
            'peer_id'    => Levin\peer_id(),
            'network_id' => hex2bin('1230f171610441611731008216a1a110'),
            'genesis'    => hex2bin('418015bb9ae982a1975da7d79277c2705727a56894ba0fb246adaabb1f4632e3'),
        ];
    }

    /**
     * @return int
     */
    public function getCommandCode() : int
    {
        return self::P2P_COMMANDS_POOL_BASE + 1;
    }
}
