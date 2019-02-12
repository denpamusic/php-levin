<?php

namespace Denpa\Levin\Types;

class Boolean extends Type implements BoostSerializable
{
    /**
     * @return string
     */
    protected function getTypeCode() : string
    {
        return 'C';
    }

    /**
     * @return \Denpa\Levin\Types\Ubyte
     */
    public function getSerializeType() : Ubyte
    {
        return new Ubyte(self::SERIALIZE_TYPE_BOOL);
    }
}
