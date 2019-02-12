<?php

namespace Denpa\Levin\Types;

class Uint16 extends Type implements BoostSerializable
{
    /**
     * @return string
     */
    protected function getTypeCode() : string
    {
        return $this->isBigEndian() ? 'n' : 'v';
    }

    /**
     * @return \Denpa\Levin\Types\Ubyte
     */
    public function getSerializeType() : Ubyte
    {
        return new Ubyte(self::SERIALIZE_TYPE_UINT16);
    }
}
