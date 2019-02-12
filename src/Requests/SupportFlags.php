<?php

namespace Denpa\Levin\Requests;

use Denpa\Levin;
use Denpa\Levin\Command;
use Denpa\Levin\Section\Section;

class SupportFlags extends Command implements RequestInterface
{
    /**
     * @var int
     */
    const P2P_SUPPORT_FLAG_FLUFFY_BLOCKS = 0x01;

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
        return new Section([
            'support_flags' => Levin\ubyte(self::P2P_SUPPORT_FLAG_FLUFFY_BLOCKS)
        ]);
    }

    /**
     * @return int
     */
    public function getCommandCode() : int
    {
        return self::P2P_COMMANDS_POOL_BASE + 7;
    }
}
