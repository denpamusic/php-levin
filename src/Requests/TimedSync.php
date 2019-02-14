<?php

namespace Denpa\Levin\Requests;

use Denpa\Levin;
use Denpa\Levin\Command;
use Denpa\Levin\Section\Section;

class TimedSync extends Command implements RequestInterface
{
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
        return new Section();
    }

    /**
     * @return array
     */
    protected function defaultVars() : array
    {
        return [
            'genesis' => hex2bin('418015bb9ae982a1975da7d79277c2705727a56894ba0fb246adaabb1f4632e3'),
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
