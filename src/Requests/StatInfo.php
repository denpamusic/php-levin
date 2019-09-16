<?php

declare(strict_types=1);

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
        return Levin\section();
    }

    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function response() : Section
    {
        return Levin\section();
    }

    /**
     * @return int
     */
    public function getCommandCode() : int
    {
        return self::P2P_COMMANDS_POOL_BASE + 4;
    }
}
