<?php

namespace Denpa\Levin\Types;

class Uint64 extends Type implements BoostSerializable
{
    /**
     * @return string
     */
    public function getTypeCode() : string
    {
        return $this->isBigEndian() ? 'J' : 'P';
    }

    /**
     * @return \Denpa\Levin\Types\Ubyte
     */
    public function getSerializeType() : Ubyte
    {
        return new Ubyte(self::SERIALIZE_TYPE_UINT64);
    }
}
