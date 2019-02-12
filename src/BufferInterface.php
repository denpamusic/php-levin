<?php

namespace Denpa\Levin;

interface BufferInterface
{
    /**
     * @return string
     */
    public function toBinary() : string;

    /**
     * @return string
     */
    public function toHex() : string;

    /**
     * @return int
     */
    public function getByteSize() : int;
}
