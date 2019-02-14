<?php

namespace Denpa\Levin\Requests;

use Denpa\Levin;
use Denpa\Levin\Command;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Traits\Peerlist;

class TimedSync extends Command implements RequestInterface
{
    use Peerlist;

    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function request() : Section
    {
        return new Section([
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
        list($localPeerlist, $localPeerlistNew) = $this->localPeerlist();

        return new Section([
            'local_time'   => Levin\uint64le(time()),
            'payload_data' => new Section([
                'cumulative_difficulty' => Levin\uint64le(1),
                'current_height'        => Levin\uint64le(1),
                'top_id'                => Levin\bytestring($this->genesis),
                'top_version'           => Levin\ubyte(1),
            ]),
            'local_peerlist_new' => Levin\bytearray($localPeerlistNew),
            'local_peerlist'     => Levin\bytestring($localPeerlist),
        ]);
    }

    /**
     * @return array
     */
    protected function defaultVars() : array
    {
        return [
            'genesis'  => hex2bin('418015bb9ae982a1975da7d79277c2705727a56894ba0fb246adaabb1f4632e3'),
            'peerlist' => [],
        ];
    }

    /**
     * @return int
     */
    public function getCommandCode() : int
    {
        return self::P2P_COMMANDS_POOL_BASE + 2;
    }
}
