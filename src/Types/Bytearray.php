<?php

namespace Denpa\Levin\Types;

use ArrayAccess;
use Countable;
use Denpa\Levin\Section;
use Denpa\Levin\Traits\Arrayable;
use IteratorAggregate;
use InvalidArgumentException;

class Bytearray implements
    BoostSerializable,
    ArrayAccess,
    Countable,
    IteratorAggregate,
    TypeInterface
{
    use Arrayable;

    /**
     * @var array
     */
    protected $entries = [];

    /**
     * @var \Denpa\Levin\Types\Ubyte|null
     */
    protected $type = null;

    /**
     * @param array $entries
     *
     * @return void
     */
    public function __construct(array $entries = [])
    {
        foreach ($entries as $entry) {
            $this->validate($entry);
        }

        $this->entries = $entries;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value) : void
    {
        $this->validate($value);
        $this->entries[] = $value;
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
        $result = '';

        $result .= new Varint(count($this->entries));

        foreach ($this->entries as $entry) {
            if ($entry instanceof Bytestring || $entry instanceof Section) {
                $result .= new Varint(count($entry));
            }

            $result .= $entry;
        }

        return $result;
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
        if (is_null($this->type)) {
            return new Ubyte(BoostSerializable::SERIALIZE_TYPE_ARRAY);
        }

        return $this->type->or(BoostSerializable::SERIALIZE_FLAG_ARRAY);
    }

    /**
     * @param mixed $value
     *
     * @return void
     */
    protected function validate($value) : void
    {
        if (!$value instanceof BoostSerializable) {
            throw new InvalidArgumentException(
                'Array entries must be serializable'
            );
        }

        if ($this->type && ($this->type != $value->getSerializeType())) {
            throw new InvalidArgumentException(
                'Array entries must be of the same type'
            );
        }

        $this->type = $value->getSerializeType();
    }
}
