<?php

namespace Denpa\Levin\Types;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Denpa\Levin\Traits\Arrayable;

class Bytearray implements
    BoostSerializable,
    ArrayAccess,
    Countable,
    IteratorAggregate,
    TypeInterface
{
    use Arrayable;

    /**
     * @param array $entries
     *
     * @return void
     */
    public function __construct(array $entries = [])
    {
        $this->entries = $entries;
    }

    /**
     * @return bool
     */
    public function isBigEndian() : bool
    {
        return false;
    }

    /**
     * @return int
     */
    public function getByteSize() : int
    {
        return strlen($this->toBinary());
    }

    /**
     * @return string
     */
    public function toHex() : string
    {
        return bin2hex($this->toBinary());
    }

    /**
     * @return string
     */
    public function toBinary() : string
    {
        return implode('', $this->entries);
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->toBinary();
    }

    /**
     * @return \Denpa\Levin\Types\Ubyte
     */
    public function getSerializeType() : Ubyte
    {
        return new Ubyte(self::SERIALIZE_TYPE_ARRAY);
    }
}
