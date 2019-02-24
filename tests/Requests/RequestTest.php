<?php

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin\Tests\CommandTest;
use Denpa\Levin\Requests\RequestInterface;

abstract class RequestTest extends CommandTest
{
    /**
     * @var int
     */
    protected $commandBase = RequestInterface::P2P_COMMANDS_POOL_BASE;
}
