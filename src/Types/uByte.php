<?php

namespace Denpa\Levin\Types;

class uByte extends Type implements BoostSerializable
{
    /**
     * @return string
     */
    protected function getTypeCode() : string
    {
        return 'C';
    }

    /**
     * @return \Denpa\Levin\Types\uByte
     */
    public function getSerializeType() : uByte
    {
        return new uByte(self::SERIALIZE_TYPE_UINT8);
    }
}
