<?php

namespace Denpa\Levin\Traits;

use Denpa\Levin;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Types\Uint64;

trait Peerlist
{
    /**
     * @return array
     */
    protected function localPeerlist() : array
    {
        $peerList = [];
        $peerListNew = [];

        foreach ($this->peerlist as $peer) {
            $this->peerDefaults($peer);

            $peerList[] = Levin\uint32($peer['ip']);
            $peerList[] = Levin\uint32le($peer['port']);
            $peerList[] = $peer['id'] instanceof Uint64 ?
                $peer['id'] : Levin\uint64le($peer['id']);

            $peerList[] = Levin\int64le($peer['last_seen']);
            $peerListNew[] = new Section([
                'adr' => new Section([
                    'addr' => new Section([
                        'm_ip'   => Levin\uint32le($peer['ip']),
                        'm_port' => Levin\uint32le($peer['port']),
                    ]),
                    'type' => Levin\uint8le($peer['type'] ?? 0),
                ]),
                'id'        => $peer['id'] instanceof Uint64 ?
                    $peer['id'] : Levin\uint64le($peer['id']),
                'last_seen' => Levin\int64le($peer['last_seen'] ?? time()),
            ]);
        }

        return [implode('', $peerList), $peerListNew];
    }

    /**
     * @param array &$peer
     *
     * @return void
     */
    protected function peerDefaults(array &$peer) : void
    {
        $peer = [
            'ip'        => ip2long($peer['ip'] ?? '127.0.0.1'),
            'port'      => $peer['port'] ?? 0,
            'type'      => $peer['type'] ?? 0,
            'id'        => $peer['id'] ?? Levin\peer_id(bin2hex(random_bytes(4))),
            'last_seen' => $peer['last_seen'] ?? time(),
        ];
    }
}
