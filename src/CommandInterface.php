<?php

namespace Denpa\Levin;

use Denpa\Levin\Types\Uint32;

interface CommandInterface
{
    /**
     * @return \Denpa\Levin\Types\Uint32
     */
    public function getCommand() : Uint32;

    /**
     * @return int
     */
    public function getCommandCode() : int;
}
