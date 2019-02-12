<?php

namespace Denpa\Levin\Types;

class Int8 extends SignedInt implements BoostSerializable
{
    /**
     * @return string
     */
    protected function getTypeCode() : string
    {
        return 'c';
    }

    /**
     * @return \Denpa\Levin\Types\uByte
     */
    public function getSerializeType() : uByte
    {
        return new uByte(self::SERIALIZE_TYPE_INT8);
    }
}
