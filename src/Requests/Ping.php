<?php

namespace Denpa\Levin\Requests;

use Denpa\Levin;
use Denpa\Levin\Command;
use Denpa\Levin\Section\Section;

class Ping extends Command implements RequestInterface
{
    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function request() : Section
    {
        return Levin\section();
    }

    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function response() : Section
    {
        return Levin\section([
            'status'  => Levin\bytestring('OK'),
            'peer_id' => $this->peer_id,
        ]);
    }

    /**
     * @return array
     */
    protected function defaultVars() : array
    {
        return [
            'peer_id' => Levin\peer_id(),
        ];
    }

    /**
     * @return int
     */
    public function getCommandCode() : int
    {
        return self::P2P_COMMANDS_POOL_BASE + 3;
    }
}
