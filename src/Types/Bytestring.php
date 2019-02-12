<?php

namespace Denpa\Levin\Types;

class Bytestring extends Type implements BoostSerializable
{
    /**
     * @return string
     */
    protected function getTypeCode() : string
    {
        return 'A*';
    }

    /**
     * @return \Denpa\Levin\Types\Ubyte
     */
    public function getSerializeType() : Ubyte
    {
        return new Ubyte(self::SERIALIZE_TYPE_STRING);
    }

    /**
     * @param resource $socket
     *
     * @return \Levin\Types\Type
     */
    public function readFrom($socket) : Type
    {
        $length = (new Varint(0))->readFrom($socket)->toInt();

        return new self(fread($socket, $length));
    }
}
