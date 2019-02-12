<?php

namespace Denpa\Levin\Notifications;

use Denpa\Levin\Command;
use Denpa\Levin\Section\Section;

class ResponseGetObjects extends Command implements NotificationInterface
{
    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function request() : Section
    {
        return new Section();
    }

    /**
     * @return int
     */
    public function getCommandCode() : int
    {
        return self::BC_COMMANDS_POOL_BASE + 4;
    }
}
