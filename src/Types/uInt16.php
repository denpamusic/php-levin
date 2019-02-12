<?php

namespace Denpa\Levin\Types;

class uInt16 extends Type implements BoostSerializable
{
    /**
     * @return string
     */
    protected function getTypeCode() : string
    {
        return $this->isBigEndian() ? 'n' : 'v';
    }

    /**
     * @return \Denpa\Levin\Types\uByte
     */
    public function getSerializeType() : uByte
    {
        return new uByte(self::SERIALIZE_TYPE_UINT16);
    }
}