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
        return Levin\section([
            'payload_data' => Levin\section([
                'cumulative_difficulty' => Levin\uint64le($this->cumulative_difficulty),
                'current_height'        => Levin\uint64le($this->current_height),
                'top_id'                => Levin\bytestring($this->top_id),
                'top_version'           => Levin\uint8($this->top_version),
            ]),
        ]);
    }

    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function response() : Section
    {
        list($localPeerlist, $localPeerlistNew) = $this->localPeerlist();

        return Levin\section([
            'local_time'   => Levin\uint64le(time()),
            'payload_data' => Levin\section([
                'cumulative_difficulty' => Levin\uint64le($this->cumulative_difficulty),
                'current_height'        => Levin\uint64le($this->current_height),
                'top_id'                => Levin\bytestring($this->top_id),
                'top_version'           => Levin\uint8($this->top_version),
            ]),
            'local_peerlist_new' => Levin\bytearray($localPeerlistNew, Levin\section()),
            'local_peerlist'     => Levin\bytestring($localPeerlist),
        ]);
    }

    /**
     * @return array
     */
    protected function defaultVars() : array
    {
        return [
            'cumulative_difficulty' => 1,
            'current_height'        => 1,
            'top_version'           => 1,
            'top_id'                => hex2bin('418015bb9ae982a1975da7d79277c2705727a56894ba0fb246adaabb1f4632e3'),
            'peerlist'              => [],
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
