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
        return Levin\section();
    }

    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function response() : Section
    {
        return Levin\section([
            'support_flags' => Levin\ubyte($this->support_flags),
        ]);
    }

    /**
     * @return array
     */
    protected function defaultVars() : array
    {
        return [
            'support_flags' => self::P2P_SUPPORT_FLAG_FLUFFY_BLOCKS,
        ];
    }

    /**
     * @return int
     */
    public function getCommandCode() : int
    {
        return self::P2P_COMMANDS_POOL_BASE + 7;
    }
}
