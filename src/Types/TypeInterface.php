<?php

declare(strict_types=1);

namespace Denpa\Levin\Types;

interface TypeInterface
{
    /**
     * @var int
     */
    const LE = 0;

    /**
     * @var int
     */
    const BE = 1;

    /**
     * @return string
     */
    public function toBinary() : string;

    /**
     * @return string
     */
    public function toHex() : string;

    /**
     * @return bool
     */
    public function isBigEndian() : bool;

    /**
     * @return int
     */
    public function getByteSize() : int;
}
