<?php

declare(strict_types=1);

namespace Denpa\Levin\Notifications;

use Denpa\Levin;
use Denpa\Levin\Command;
use Denpa\Levin\Section\Section;

class NewFluffyBlock extends Command implements NotificationInterface
{
    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function request() : Section
    {
        return Levin\section([
            'b'                         => Levin\bytestring($this->block),
            'current_blockchain_height' => Levin\uint64le($this->current_blockchain_height),
        ]);
    }

    /**
     * @return array
     */
    protected function defaultVars() : array
    {
        return [
            'block'                     => '',
            'current_blockchain_height' => 0,
        ];
    }

    /**
     * @return int
     */
    public function getCommandCode() : int
    {
        return self::BC_COMMANDS_POOL_BASE + 8;
    }
}
