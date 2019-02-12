<?php

namespace Denpa\Levin\Types;

class uInt64 extends Type implements BoostSerializable
{
    /**
     * @return string
     */
    public function getTypeCode() : string
    {
        return $this->isBigEndian() ? 'J' : 'P';
    }

    /**
     * @return \Denpa\Levin\Types\uByte
     */
    public function getSerializeType() : uByte
    {
        return new uByte(self::SERIALIZE_TYPE_UINT64);
    }
}
