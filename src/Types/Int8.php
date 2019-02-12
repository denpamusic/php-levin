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
     * @return \Denpa\Levin\Types\Ubyte
     */
    public function getSerializeType() : Ubyte
    {
        return new Ubyte(self::SERIALIZE_TYPE_INT8);
    }
}
