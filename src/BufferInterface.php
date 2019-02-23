<?php

namespace Denpa\Levin;

interface BufferInterface
{
    /**
     * Converts buffer to binary.
     *
     * @return string
     */
    public function toBinary() : string;

    /**
     * Converts buffer to hex.
     *
     * @return string
     */
    public function toHex() : string;

    /**
     * Gets buffer size in bytes.
     *
     * @return int
     */
    public function getByteSize() : int;
}
