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
     * @return \Denpa\Levin\Types\Uint8
     */
    public function getSerializeType() : Uint8
    {
        return new Uint8(self::SERIALIZE_TYPE_UINT64);
    }
}
