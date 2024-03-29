<?php

declare(strict_types=1);

namespace Denpa\Levin\Section;

use Denpa\Levin;
use Denpa\Levin\Connection;
use Denpa\Levin\Exceptions\SignatureMismatchException;
use Denpa\Levin\Exceptions\UnexpectedTypeException;
use Denpa\Levin\Types\BoostSerializable;
use Denpa\Levin\Types\Bytearray;
use Denpa\Levin\Types\Bytestring;
use Denpa\Levin\Types\Int16;
use Denpa\Levin\Types\Int32;
use Denpa\Levin\Types\Int64;
use Denpa\Levin\Types\Int8;
use Denpa\Levin\Types\Uint16;
use Denpa\Levin\Types\Uint32;
use Denpa\Levin\Types\Uint64;
use Denpa\Levin\Types\Uint8;
use Denpa\Levin\Types\Varint;

class Reader
{
    /**
     * @var \Denpa\Levin\Connection
     */
    protected $connection;

    /**
     * @var array
     */
    protected $types = [
        Section::SERIALIZE_TYPE_UINT64 => Uint64::class,
        Section::SERIALIZE_TYPE_INT64  => Int64::class,
        Section::SERIALIZE_TYPE_UINT32 => Uint32::class,
        Section::SERIALIZE_TYPE_INT32  => Int32::class,
        Section::SERIALIZE_TYPE_UINT16 => Uint16::class,
        Section::SERIALIZE_TYPE_INT16  => Int16::class,
        Section::SERIALIZE_TYPE_UINT8  => Uint8::class,
        Section::SERIALIZE_TYPE_INT8   => Int8::class,
        Section::SERIALIZE_TYPE_OBJECT => Section::class,
        Section::SERIALIZE_TYPE_STRING => Bytestring::class,
    ];

    /**
     * @param \Denpa\Levin\Connection $connection
     *
     * @return void
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function read(): Section
    {
        $signatures = [
            $this->connection->read(new Uint32()),
            $this->connection->read(new Uint32()),
            $this->connection->read(new Uint8()),
        ];

        foreach (Levin\section()->getSignatures() as $key => $signature) {
            if ($signatures[$key]->toHex() != $signature->toHex()) {
                throw new SignatureMismatchException($signature, 'Section signature mismatch');
            }
        }

        return $this->readSection();
    }

    /**
     * @return \Denpa\Levin\Section\Section
     */
    protected function readSection(): Section
    {
        $section = Levin\section();

        $count = $this->connection->read(new Varint())->toInt();

        while ($count > 0) {
            $section[$this->readName()] = $this->loadEntries();
            $count--;
        }

        return $section;
    }

    /**
     * @return string
     */
    protected function readName(): string
    {
        $length = $this->connection->read(new Uint8());

        return $this->connection->readBytes($length->toInt());
    }

    /**
     * @return \Denpa\Levin\Types\BoostSerializable
     */
    protected function loadEntries(): BoostSerializable
    {
        $type = $this->connection->read(new Uint8())->toInt();

        if (($type & Section::SERIALIZE_FLAG_ARRAY) != 0) {
            return $this->readArrayEntry($type);
        }

        if ($type == Section::SERIALIZE_TYPE_ARRAY) {
            return $this->readEntryArrayEntry();
        }

        return $this->readValue($type);
    }

    /**
     * @return \Denpa\Levin\Types\Bytearray
     */
    protected function readEntryArrayEntry(): Bytearray
    {
        $type = $this->connection->read(new Uint8())->toInt();

        if (($type & Section::SERIALIZE_FLAG_ARRAY) != 0) {
            throw new UnexpectedTypeException('Incorrect type sequence');
        }

        return $this->readArrayEntry($type);
    }

    /**
     * @param int $type
     *
     * @return \Denpa\Levin\Types\Bytearray
     */
    protected function readArrayEntry(int $type): Bytearray
    {
        $result = [];
        $type &= ~Section::SERIALIZE_FLAG_ARRAY;
        $count = $this->connection->read(new Varint())->toInt();

        while ($count > 0) {
            $result[] = $this->readValue($type);
            $count--;
        }

        return Levin\bytearray($result, new $this->types[$type]());
    }

    /**
     * @param int $type
     *
     * @return \Denpa\Levin\Types\BoostSerializable
     */
    protected function readValue(int $type): BoostSerializable
    {
        if (!array_key_exists($type, $this->types)) {
            throw new UnexpectedTypeException(
                "Cannot unserialize unknown type [$type]"
            );
        }

        if ($this->types[$type] == Section::class) {
            return $this->readSection();
        }

        return $this->connection->read(new $this->types[$type]());
    }
}
