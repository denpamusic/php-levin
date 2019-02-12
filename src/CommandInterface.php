<?php

namespace Denpa\Levin;

use Denpa\Levin\Types\uInt32;

interface CommandInterface
{
    /**
     * @return \Denpa\Levin\Types\uInt32
     */
    public function getCommand() : uInt32;

    /**
     * @return int
     */
    public function getCommandCode() : int;
}
