<?php

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin\Tests\CommandTest;
use Denpa\Levin\Notifications\NotificationInterface;

abstract class NotificationTest extends CommandTest
{
    /**
     * @var int
     */
    protected $commandBase = NotificationInterface::BC_COMMANDS_POOL_BASE;
}
