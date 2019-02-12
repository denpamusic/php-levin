<?php

namespace Denpa\Levin\Requests;

use Denpa\Levin;
use Denpa\Levin\Command;
use Denpa\Levin\Section\Section;

class StatInfo extends Command implements RequestInterface
{
    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function request() : Section
    {
        return new Section;
    }

    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function response() : Section
    {
        return new Section;
    }

    /**
     * @return int
     */
    public function getCommandCode() : int
    {
        return self::P2P_COMMANDS_POOL_BASE + 4;
    }
}
