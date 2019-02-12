<?php

namespace Denpa\Levin\Section;

use Denpa\Levin;
use Denpa\Levin\Types\BoostSerializable;
use Denpa\Levin\Types\Bytearray;

class Reader
{
    /**
     * @var resourse
     */
    protected $fp;

    /**
     * @param resourse $fp
     *
     * @return void
     */
    public function __construct($fp)
    {
        $this->fp = $fp;
    }

    /**
     * @return \Denpa\Levin\Section\Section
     */
    public function read() : Section
    {
        $signatures = [
            fread($this->fp, count(Levin\uint32le())),
            fread($this->fp, count(Levin\uint32le())),
            fread($this->fp, count(Levin\ubyte())),
        ];

        return $this->readSection();
    }

    /**
     * @return \Denpa\Levin\Section\Section
     */
    protected function readSection() : Section
    {
        $section = new Section();

        $count = Levin\varint()->readFrom($this->fp)->toInt();

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
        $length = Levin\ubyte(fread($this->fp, count(Levin\ubyte())));
        $name = fread($this->fp, $length->toInt());

        return $name;
    }

    /**
     * @return \Denpa\Levin\Types\BoostSerializable
     */
    protected function loadEntries() : BoostSerializable
    {
        $type = Levin\ubyte(fread($this->fp, count(Levin\ubyte())))->toInt();

        if (($type & Section::SERIALIZE_FLAG_ARRAY) != 0) {
            return $this->readArrayEntry($type);
        } elseif ($type == Section::SERIALIZE_TYPE_ARRAY) {
            return $this->readEntryArrayEntry($type);
        } else {
            return $this->readValue($type);
        }
    }

    /**
     * @param int $type
     *
     * @return \Denpa\Levin\Types\Bytearray
     */
    protected function readEntryArrayEntry($type) : Bytearray
    {
        $type = Levin\ubyte(fread($this->fp, count(Levin\ubyte())))->toInt();

        if (($type & SERIALIZE_FLAG_ARRAY) != 0) {
            throw new \Exception('Incorrect array sequence');
        }

        return readArrayEntry($type);
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
        $size = Levin\varint()->readFrom($this->fp)->toInt();

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
        switch ($type) {
            case Section::SERIALIZE_TYPE_UINT64:
                return Levin\uint64le(fread($this->fp, count(Levin\uint64le())));
            case Section::SERIALIZE_TYPE_INT64:
                return Levin\int64le(fread($this->fp, count(Levin\int64le())));
            case Section::SERIALIZE_TYPE_UINT32:
                return Levin\uint32le(fread($this->fp, count(Levin\uint32le())));
            case Section::SERIALIZE_TYPE_INT32:
                return Levin\int32le(fread($this->fp, count(Levin\int32le())));
            case Section::SERIALIZE_TYPE_UINT16:
                return Levin\uint16le(fread($this->fp, count(Levin\uint16le())));
            case Section::SERIALIZE_TYPE_INT16:
                return Levin\int16le(fread($this->fp, count(Levin\int16le())));
            case Section::SERIALIZE_TYPE_UINT8:
                return Levin\uint8le(fread($this->fp, count(Levin\uint8le())));
            case Section::SERIALIZE_TYPE_INT8:
                return Levin\int8le(fread($this->fp, count(Levin\int8le())));
            case Section::SERIALIZE_TYPE_OBJECT:
                return $this->readSection();
            case Section::SERIALIZE_TYPE_STRING:
                $length = Levin\varint()->readFrom($this->fp)->toInt();

                return Levin\bytestring(fread($this->fp, $length));
            default:
                throw new \Exception("Cannot unserialize unknown type [$type]");
        }
    }
}
