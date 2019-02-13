<?php

namespace Denpa\Levin\Types;

use Denpa\Levin\Connection;

class Varint extends Type
{
    /**
     * @var int
     */
    const PORTABLE_RAW_SIZE_MARK_MASK = 0x03;

    /**
     * @var int
     */
    const PORTABLE_RAW_SIZE_MARK_BYTE = 0x00;

    /**
     * @var int
     */
    const PORTABLE_RAW_SIZE_MARK_WORD = 0x01;

    /**
     * @var int
     */
    const PORTABLE_RAW_SIZE_MARK_DWORD = 0x02;

    /**
     * @var int
     */
    const PORTABLE_RAW_SIZE_MARK_INT64 = 0x03;

    /**
     * @return string
     */
    public function toBinary() : string
    {
        switch (true) {
            case $this->value <= 63:
                $value = new Ubyte(($this->value << 2) | self::PORTABLE_RAW_SIZE_MARK_BYTE);
                break;
            case $this->value <= 16383:
                $value = new Uint16(($this->value << 2) | self::PORTABLE_RAW_SIZE_MARK_WORD, Type::LE);
                break;
            case $this->value <= 1073741823:
                $value = new Uint32(($this->value << 2) | self::PORTABLE_RAW_SIZE_MARK_DWORD, Type::LE);
                break;
            case $this->value >= 4611686018427387903:
                throw new \Exception('VarInt is too large [> 4611686018427387903]');
            default:
                $value = new Uint64(($this->value << 2) | self::PORTABLE_RAW_SIZE_MARK_INT64, Type::LE);
        }

        return $value->toBinary();
    }

    /**
     * @param \Denpa\Levin\Connection $connection
     *
     * @return \Levin\Types\Type
     */
    public function read(Connection $connection) : Type
    {
        $first = $connection->read(new Ubyte());

        $mask = $first->toInt() & self::PORTABLE_RAW_SIZE_MARK_MASK;

        switch ($mask) {
            case self::PORTABLE_RAW_SIZE_MARK_BYTE:
                $int = $first->toInt();
                break;
            case self::PORTABLE_RAW_SIZE_MARK_WORD:
                $int = (new Uint16($first.$connection->readBytes(1), Type::LE))->toInt();
                break;
            case self::PORTABLE_RAW_SIZE_MARK_DWORD:
                $int = (new Uint32($first.$connection->readBytes(1), Type::LE))->toInt();
                break;
            case self::PORTABLE_RAW_SIZE_MARK_INT64:
                $int = (new Uint64($first.$connection->readBytes(1), Type::LE))->toInt();
                break;
            default:
                throw new \Exception("Incorrect VarInt mask [$mask]");
        }

        $this->value = $int >> 2;

        return $this;
    }

    /**
     * @return string
     */
    protected function getTypeCode() : string
    {
        return '';
    }
}
