<?php

namespace Denpa\Levin\Types;

class Int64 extends SignedInt implements BoostSerializable
{
    /**
     * @return string
     */
    protected function getTypeCode() : string
    {
        return 'q';
    }

    /**
     * @return \Denpa\Levin\Types\Ubyte
     */
    public function getSerializeType() : Ubyte
    {
        return new Ubyte(self::SERIALIZE_TYPE_UINT64);
    }
}
