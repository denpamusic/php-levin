<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin\Requests\RequestInterface;
use Denpa\Levin\Tests\CommandTest;

abstract class RequestTest extends CommandTest
{
    /**
     * @var int
     */
    protected $commandBase = RequestInterface::P2P_COMMANDS_POOL_BASE;
}
