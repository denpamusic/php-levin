<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin\Notifications\NotificationInterface;
use Denpa\Levin\Tests\CommandTest;

abstract class NotificationTest extends CommandTest
{
    /**
     * @var int
     */
    protected $commandBase = NotificationInterface::BC_COMMANDS_POOL_BASE;
}
