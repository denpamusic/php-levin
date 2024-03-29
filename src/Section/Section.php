<?php

declare(strict_types=1);

namespace Denpa\Levin\Section;

use ArrayAccess;
use Countable;
use Denpa\Levin;
use Denpa\Levin\BufferInterface;
use Denpa\Levin\Exceptions\UnexpectedTypeException;
use Denpa\Levin\Traits\Arrayable;
use Denpa\Levin\Types\BoostSerializable;
use Denpa\Levin\Types\Bytestring;
use Denpa\Levin\Types\Uint8;
use IteratorAggregate;

class Section implements
    SectionInterface,
    ArrayAccess,
    IteratorAggregate,
    Countable,
    BoostSerializable,
    BufferInterface
{
    use Arrayable;

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
            Levin\uint8(self::PORTABLE_STORAGE_FORMAT_VER),
        ];
    }

    /**
     * @param string                               $key
     * @param \Denpa\Levin\Types\BoostSerializable $value
     *
     * @return self
     */
    public function add(string $key, BoostSerializable $value): self
    {
        $this->entries[$key] = $value;

        return $this;
    }

    /**
     * @return \Denpa\Levin\Types\Uint8
     */
    public function getSerializeType(): Uint8
    {
        return new Uint8(self::SERIALIZE_TYPE_OBJECT);
    }

    /**
     * @return array
     */
    public function getSignatures(): array
    {
        return $this->signatures;
    }

    /**
     * @return string
     */
    public function toBinary(): string
    {
        $result = implode('', $this->signatures);
        $result .= Levin\varint(count($this));
        $result .= $this->serialize();

        return $result;
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
    public function getByteSize(): int
    {
        return strlen($this->toBinary());
    }

    /**
     * @return int
     */
    public function getInternalByteSize(): int
    {
        return strlen($this->serialize());
    }

    /**
     * @return array
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @return string
     */
    public function serialize(): string
    {
        $result = '';

        foreach ($this->entries as $key => $entry) {
            if (!$entry instanceof BoostSerializable) {
                throw new UnexpectedTypeException(
                    "Cannot serialize unserializable item [$key]"
                );
            }

            $result .= Levin\uint8(strlen($key));
            $result .= $key;
            $result .= $entry->getSerializeType();

            if ($entry instanceof Bytestring || $entry instanceof self) {
                $result .= Levin\varint(count($entry));
            }

            $result .= $entry;
        }

        return $result;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->serialize();
    }
}
