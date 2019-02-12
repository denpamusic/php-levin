<?php

namespace Denpa\Levin\Types;

use Countable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;

class Bytearray implements  BoostSerializable, ArrayAccess,
                            Countable, IteratorAggregate, TypeInterface
{
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
     * @return \Denpa\Levin\Types\uByte
     */
    public function getSerializeType() : uByte
    {
        return new uByte(self::SERIALIZE_TYPE_ARRAY);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value) : void
    {
        if (is_null($offset)) {
            $this->entries[] = $value;
        } else {
            $this->entries[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->entries[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset) : void
    {
        unset($this->entries[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->entries[$offset] ?? null;
    }

    /**
     * @return int
     */
    public function count() : int
    {
        return count($this->entries);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->entries);
    }
}
