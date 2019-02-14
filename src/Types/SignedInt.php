<?php

namespace Denpa\Levin\Types;

abstract class SignedInt extends Type
{
    /**
     * @return int
     */
    public function machineEndianness() : int
    {
        return (int) !unpack('S', "\x01\x00")[1] === 1;
    }

    /**
     * @return string
     */
    public function toBinary() : string
    {
        $endian = $this->machineEndianness() == self::BE ?
            $this->isBigEndian() : !$this->isBigEndian();

        return $endian ? parent::toBinary() : strrev(parent::toBinary());
    }
}
