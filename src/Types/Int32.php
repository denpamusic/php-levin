<?php

namespace Denpa\Levin\Types;

class Int32 extends SignedInt implements BoostSerializable
{
    /**
     * @return string
     */
    protected function getTypeCode() : string
    {
        return 'l';
    }

    /**
     * @return \Denpa\Levin\Types\Uint8
     */
    public function getSerializeType() : Uint8
    {
        return new Uint8(self::SERIALIZE_TYPE_INT32);
    }
}
