<?php

namespace Denpa\Levin\Types;

class Uint32 extends Type implements BoostSerializable
{
    /**
     * @return string
     */
    protected function getTypeCode() : string
    {
        return $this->isBigEndian() ? 'N' : 'V';
    }

    /**
     * @return \Denpa\Levin\Types\Ubyte
     */
    public function getSerializeType() : Ubyte
    {
        return new Ubyte(self::SERIALIZE_TYPE_UINT32);
    }
}
