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
     * @return \Denpa\Levin\Types\Uint8
     */
    public function getSerializeType() : Uint8
    {
        return new Uint8(self::SERIALIZE_TYPE_STRING);
    }

    /**
     * @param \Denpa\Levin\Connection $connection
     *
     * @return \Levin\Types\Type
     */
    public function read(Connection $connection) : Type
    {
        $length = $connection->read(new Varint())->toInt();

        if ($length == 0) {
            return new self();
        }

        return new self($connection->readBytes($length));
    }
}
