<?php

declare(strict_types=1);

namespace Denpa\Levin;

use Denpa\Levin\Types\Uint32;

interface CommandInterface
{
    /**
     * Gets command as an uint32 type.
     *
     * @return \Denpa\Levin\Types\Uint32
     */
    public function getCommand(): Uint32;

    /**
     * Gets command as an interger.
     *
     * @return int
     */
    public function getCommandCode(): int;
}
