<?php

namespace Denpa\Levin\Section;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Denpa\Levin;
use Denpa\Levin\BufferInterface;
use Denpa\Levin\Types\BoostSerializable;
use Denpa\Levin\Types\Bytestring;
use Denpa\Levin\Types\TypeInterface;
use Denpa\Levin\Types\uByte;
use IteratorAggregate;

class Section implements
    SectionInterface,
    ArrayAccess,
    IteratorAggregate,
    Countable,
    BoostSerializable,
    BufferInterface
{
    /**
     * @var array
     */
    protected $entries = [];

    /**
     * @var array
     */
    protected $signatures = [];

    /**
     * @param array $entries
     *
     * @return void
     */
    public function __construct(array $entries = [])
    {
        $this->entries = $entries;

        $this->signatures = [
            Levin\uint32le(self::PORTABLE_STORAGE_SIGNATUREA),
            Levin\uint32le(self::PORTABLE_STORAGE_SIGNATUREB),
            Levin\ubyte(self::PORTABLE_STORAGE_FORMAT_VER),
        ];
    }

    /**
     * @param string                           $key
     * @param \Denpa\Levin\Types\TypeInterface $value
     *
     * @return self
     */
    public function add(string $key, TypeInterface $value) : self
    {
        $this->entries[$key] = $value;

        return $this;
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

    /**
     * @return \Denpa\Levin\Types\uByte
     */
    public function getSerializeType() : uByte
    {
        return new uByte(self::SERIALIZE_TYPE_OBJECT);
    }

    /**
     * @return array
     */
    public function getSignatures() : array
    {
        return $this->signatures;
    }

    /**
     * @return string
     */
    public function toBinary() : string
    {
        $result = implode('', $this->signatures);
        $result .= Levin\varint(count($this));
        $result .= $this->serialize();

        return $result;
    }

    /**
     * @return string
     */
    public function toHex() : string
    {
        return bin2hex($this->toBinary());
    }

    /**
     * @return int
     */
    public function getByteSize() : int
    {
        return strlen($this->toBinary());
    }

    /**
     * @return int
     */
    public function getInternalByteSize() : int
    {
        return strlen($this->serialize());
    }

    public function getEntries() : array
    {
        return $this->entries;
    }

    /**
     * @return string
     */
    public function serialize() : string
    {
        $result = '';

        foreach ($this->entries as $key => $item) {
            if (!$item instanceof BoostSerializable) {
                throw new \Exception("Cannot serialize unserializable item [$key]");
            }

            $result .= Levin\ubyte(strlen($key));
            $result .= $key;
            $result .= $item->getSerializeType();

            if ($item instanceof Bytestring || $item instanceof self) {
                $size = $item instanceof self ?
                    count($item) : $item->getByteSize();
                $result .= Levin\varint($size);
            }

            $result .= $item;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->serialize();
    }
}
