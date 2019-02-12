<?php

namespace Denpa\Levin\Types;

class VarInt extends Type
{
    /**
     * @var int
     */
    const PORTABLE_RAW_SIZE_MARK_MASK  = 0x03;

    /**
     * @var int
     */
    const PORTABLE_RAW_SIZE_MARK_BYTE  = 0x00;

    /**
     * @var int
     */
    const PORTABLE_RAW_SIZE_MARK_WORD  = 0x01;

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
                $value = new uByte(($this->value << 2) | self::PORTABLE_RAW_SIZE_MARK_BYTE);
                break;
            case $this->value <= 16383:
                $value = new uInt16(($this->value << 2) | self::PORTABLE_RAW_SIZE_MARK_WORD, Type::LE);
                break;
            case $this->value <= 1073741823:
                $value = new uInt32(($this->value << 2) | self::PORTABLE_RAW_SIZE_MARK_DWORD, Type::LE);
                break;
            case $this->value >= 4611686018427387903:
                throw new \Exception('VarInt is too large [> 4611686018427387903]');
            default:
                $value = new uInt64(($this->value << 2) | self::PORTABLE_RAW_SIZE_MARK_INT64, Type::LE);
        }

        return $value->toBinary();
    }

    /**
     * @param resourse $fp
     *
     * @return self
     */
    public function readFrom($fp) : self
    {
        $first = new uByte(fread($fp, sizeof(new uByte(0))));

        $mask = $first->toInt() & self::PORTABLE_RAW_SIZE_MARK_MASK;

        switch ($mask) {
            case self::PORTABLE_RAW_SIZE_MARK_BYTE:
                $int = $first->toInt();
                break;
            case self::PORTABLE_RAW_SIZE_MARK_WORD:
                $int = (new uInt16($first.fread($fp, 1), Type::LE))->toInt();
                break;
            case self::PORTABLE_RAW_SIZE_MARK_DWORD:
                $int = (new uInt32($first.fread($fp, 3), Type::LE))->toInt();
                break;
            case self::PORTABLE_RAW_SIZE_MARK_INT64:
                $int = (new uInt64($first.fread($fp, 7), Type::LE))->toInt();
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
