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
    public function getSerializeType() : self
    {
        return new self(self::SERIALIZE_TYPE_UINT8);
    }
}
