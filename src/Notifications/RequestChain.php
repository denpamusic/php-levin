<?php

namespace Denpa\Levin\Notifications;

use Denpa\Levin;
use Denpa\Levin\Command;
use Denpa\Levin\Section\Section;

class RequestChain extends Command implements NotificationInterface
{
    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function request() : Section
    {
        return Levin\section([
            'block_ids' => Levin\bytestring($this->block_ids),
        ]);
    }

    /**
     * @return array
     */
    protected function defaultVars() : array
    {
        return [
            'block_ids' => '',
        ];
    }

    /**
     * @return int
     */
    public function getCommandCode() : int
    {
        return self::BC_COMMANDS_POOL_BASE + 6;
    }
}
