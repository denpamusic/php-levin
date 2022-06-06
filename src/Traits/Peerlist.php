<?php

declare(strict_types=1);

namespace Denpa\Levin\Traits;

use Denpa\Levin;
use Denpa\Levin\Types\Uint64;

trait Peerlist
{
    /**
     * @return array
     */
    protected function localPeerlist(): array
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
            $peerListNew[] = Levin\section([
                'adr' => Levin\section([
                    'addr' => Levin\section([
                        'm_ip'   => Levin\uint32le($peer['ip']),
                        'm_port' => Levin\uint32le($peer['port']),
                    ]),
                    'type' => Levin\uint8le($peer['type']),
                ]),
                'id'        => $peer['id'] instanceof Uint64 ?
                    $peer['id'] : Levin\uint64le($peer['id']),
                'last_seen' => Levin\int64le($peer['last_seen']),
            ]);
        }

        return [implode('', $peerList), $peerListNew];
    }

    /**
     * @param array &$peer
     *
     * @return void
     */
    protected function peerDefaults(array &$peer): void
    {
        $peer = [
            'ip'        => inet_pton($peer['ip'] ?? '127.0.0.1'),
            'port'      => $peer['port'] ?? 0,
            'type'      => $peer['type'] ?? 0,
            'id'        => $peer['id'] ?? Levin\peer_id(bin2hex(random_bytes(4))),
            'last_seen' => $peer['last_seen'] ?? time(),
        ];
    }
}
