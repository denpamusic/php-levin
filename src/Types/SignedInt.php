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
        switch ($this->machineEndianness()) {
            case self::BE:
                return $this->isBigEndian() ?
                    parent::toBinary() : strrev(parent::toBinary());
            case self::LE:
                return !$this->isBigEndian() ?
                    parent::toBinary() : strrev(parent::toBinary());
        }
    }
}
