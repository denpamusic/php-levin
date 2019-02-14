<?php

namespace Denpa\Levin\Types;

use Denpa\Levin\Connection;

class Bytestring extends Type implements BoostSerializable
{
    /**
     * @return string
     */
    protected function getTypeCode() : string
    {
        return 'a*';
    }

    /**
     * @return \Denpa\Levin\Types\Ubyte
     */
    public function getSerializeType() : Ubyte
    {
        return new Ubyte(self::SERIALIZE_TYPE_STRING);
    }

    /**
     * @param \Denpa\Levin\Connection $connection
     *
     * @return \Levin\Types\Type
     */
    public function read(Connection $connection) : Type
    {
        $length = $connection->read(new Varint())->toInt();

        return new self($connection->readBytes($length));
    }
}
