<?php

namespace Denpa\Levin\Notifications;

use Denpa\Levin;
use Denpa\Levin\Command;
use Denpa\Levin\Section\Section;

class ResponseChainEntry extends Command implements NotificationInterface
{
    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function request() : Section
    {
        return Levin\section([
            'start_height'          => Levin\uint64le($this->start_height),
            'total_height'          => Levin\uint64le($this->total_height),
            'cumulative_difficulty' => Levin\uint64le($this->cumulative_difficulty),
            'm_block_ids'           => Levin\bytestring($this->m_block_ids),
        ]);
    }

    /**
     * @return array
     */
    protected function defaultVars() : array
    {
        return [
            'start_height'          => 0,
            'total_height'          => 0,
            'cumulative_difficulty' => 0,
            'm_block_ids'           => '',
        ];
    }

    /**
     * @return int
     */
    public function getCommandCode() : int
    {
        return self::BC_COMMANDS_POOL_BASE + 7;
    }
}
