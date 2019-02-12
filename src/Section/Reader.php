<?php

namespace Denpa\Levin\Section;

use Denpa\Levin;
use Denpa\Levin\Types\Int8;
use Denpa\Levin\Types\Int16;
use Denpa\Levin\Types\Int32;
use Denpa\Levin\Types\Int64;
use Denpa\Levin\Types\Uint8;
use Denpa\Levin\Types\Uint16;
use Denpa\Levin\Types\Uint32;
use Denpa\Levin\Types\Uint64;
use Denpa\Levin\Types\Bytearray;
use Denpa\Levin\Types\Bytestring;
use Denpa\Levin\Types\BoostSerializable;

class Reader
{
    /**
     * @var resourse
     */
    protected $socket;

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
     * @param resourse $socket
     *
     * @return void
     */
    public function __construct($socket)
    {
        $this->socket = $socket;
    }

    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function read() : Section
    {
        $signatures = [
            Levin\uint32le()->readFrom($this->socket),
            Levin\uint32le()->readFrom($this->socket),
            Levin\ubyte()->readFrom($this->socket),
        ];

        foreach ((new Section())->getSignatures() as $key => $signature) {
            if ($signatures[$key]->toHex() != $signature->toHex()) {
                throw new \Exception(
                    "Section signature mismatch [0x{$signature->toHex()}]"
                );
            }
        }

        return $this->readSection();
    }

    /**
     * @return \Denpa\Levin\Section\Section
     */
    protected function readSection() : Section
    {
        $section = new Section();

        $count = Levin\varint()->readFrom($this->socket)->toInt();

        while ($count > 0) {
            $section[$this->readName()] = $this->loadEntries();
            $count--;
        }

        return $section;
    }

    /**
     * @return string
     */
    protected function readName() : string
    {
        $length = Levin\ubyte()->readFrom($this->socket);
        $name = fread($this->socket, $length->toInt());

        return $name;
    }

    /**
     * @return \Denpa\Levin\Types\BoostSerializable
     */
    protected function loadEntries() : BoostSerializable
    {
        $type = Levin\ubyte()->readFrom($this->socket)->toInt();

        if (($type & Section::SERIALIZE_FLAG_ARRAY) != 0) {
            return $this->readArrayEntry($type);
        }

        if ($type == Section::SERIALIZE_TYPE_ARRAY) {
            return $this->readEntryArrayEntry($type);
        }

        return $this->readValue($type);
    }

    /**
     * @param int $type
     *
     * @return \Denpa\Levin\Types\Bytearray
     */
    protected function readEntryArrayEntry($type) : Bytearray
    {
        $type = Levin\ubyte()->readFrom($this->socket)->toInt();

        if (($type & SERIALIZE_FLAG_ARRAY) != 0) {
            throw new \Exception('Incorrect array sequence');
        }

        return $this->readArrayEntry($type);
    }

    /**
     * @param int $type
     *
     * @return \Denpa\Levin\Types\Bytearray
     */
    protected function readArrayEntry(int $type) : Bytearray
    {
        $result = [];
        $type &= ~Section::SERIALIZE_FLAG_ARRAY;
        $size = Levin\varint()->readFrom($this->socket)->toInt();

        while ($size > 0) {
            $result[] = $this->readValue($type);
            $size--;
        }

        return Levin\bytearray($result);
    }

    /**
     * @param int $type
     *
     * @return \Denpa\Levin\Types\BoostSerializable
     */
    protected function readValue(int $type) : BoostSerializable
    {
        if (!array_key_exists($type, $this->types)) {
            throw new \Exception("Cannot unserialize unknown type [$type]");
        }

        if ($this->types[$type] == Section::class) {
            return $this->readSection();
        }

        return (new $this->types[$type](null))->readFrom($this->socket);
    }
}
