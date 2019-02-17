<?php

namespace Denpa\Levin\Types;

class Uint8 extends Type implements BoostSerializable
{
    /**
     * @return string
     */
    protected function getTypeCode() : string
    {
        return 'C';
    }

    /**
     * @return \Denpa\Levin\Types\Uint8
     */
    public function getSerializeType() : Uint8
    {
        return new Uint8(self::SERIALIZE_TYPE_UINT8);
    }
}
