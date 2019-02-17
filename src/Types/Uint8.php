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
    public function getSerializeType() : self
    {
        return new self(self::SERIALIZE_TYPE_UINT8);
    }
}
