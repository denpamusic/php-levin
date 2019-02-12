<?php

namespace Denpa\Levin\Types;

use Countable;

abstract class Type implements TypeInterface, Countable
{
    /**
     * @var int
     */
    protected $endianness;

    /**
     * @param mixed $value
     * @param int   $endianness
     *
     * @return void
     */
    public function __construct($value, int $endianness = self::BE)
    {
        $this->endianness = $endianness;
        $this->value = $value;

        if (is_string($this->value)) {
            $unpacked = @unpack($this->getTypeCode(), $this->value);

            if ($unpacked === false) {
                throw new \Exception('Failed to unpack binary data');
            }

            $this->value = $unpacked[1];
        }
    }

    /**
     * @return bool
     */
    public function isBigEndian() : bool
    {
        return (bool) $this->endianness;
    }

    /**
     * @return string
     */
    public function toBinary() : string
    {
        return pack($this->getTypeCode(), $this->value);
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
    public function toInt() : int
    {
        return (int) $this->value;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->toBinary();
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
    public function count() : int
    {
        return $this->getByteSize();
    }

    /**
     * @return string
     */
    abstract protected function getTypeCode() : string;
}
