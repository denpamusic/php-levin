<?php

declare(strict_types=1);

namespace Denpa\Levin\Notifications;

use Denpa\Levin\Section\Section;

interface NotificationInterface
{
    /**
     * @var int
     */
    const BC_COMMANDS_POOL_BASE = 2000;

    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function request(): Section;
}
