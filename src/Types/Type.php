<?php

declare(strict_types=1);

namespace Denpa\Levin\Types;

use Countable;
use Denpa\Levin\Exceptions\UnpackException;

abstract class Type implements TypeInterface, Countable
{
    /**
     * @var int
     */
    protected $endianness;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param mixed $value
     * @param int   $endianness
     *
     * @return void
     */
    public function __construct($value = null, int $endianness = self::BE)
    {
        $this->endianness = $endianness;
        $this->value = $value;

        if (is_string($this->value)) {
            $unpacked = @unpack($this->getTypeCode(), $this->value);

            if ($unpacked === false || !isset($unpacked[1])) {
                throw new UnpackException(
                    "Failed to unpack binary data [{$this->value}]"
                );
            }

            $this->value = $unpacked[1];
        }
    }

    /**
     * @return bool
     */
    public function isBigEndian(): bool
    {
        return (bool) $this->endianness;
    }

    /**
     * @return string
     */
    public function toBinary(): string
    {
        return pack($this->getTypeCode(), $this->value);
    }

    /**
     * @return string
     */
    public function toHex(): string
    {
        return bin2hex($this->toBinary());
    }

    /**
     * @return int
     */
    public function toInt(): int
    {
        return (int) $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toBinary();
    }

    /**
     * @return int
     */
    public function getByteSize(): int
    {
        return strlen($this->toBinary());
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->getByteSize();
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public function or($value): self
    {
        $type = clone $this;
        $type->value |= $value instanceof self ? $value->toInt() : $value;

        return $type;
    }

    /**
     * @param mixed $value
     *
     * @return self
     */
    public function and($value): self
    {
        $type = clone $this;
        $type->value &= $value instanceof self ? $value->toInt() : $value;

        return $type;
    }

    /**
     * @return string
     */
    abstract protected function getTypeCode(): string;
}
