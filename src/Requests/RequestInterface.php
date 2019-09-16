<?php

declare(strict_types=1);

namespace Denpa\Levin\Requests;

use Denpa\Levin\Section\Section;

interface RequestInterface
{
    /**
     * @var int
     */
    const P2P_COMMANDS_POOL_BASE = 1000;

    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function request() : Section;

    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function response() : Section;
}
