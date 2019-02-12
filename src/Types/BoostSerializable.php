<?php

namespace Denpa\Levin\Types;

interface BoostSerializable
{
    /**
     * @var int
     */
    const SERIALIZE_TYPE_INT64 = 1;

    /**
     * @var int
     */
    const SERIALIZE_TYPE_INT32 = 2;

    /**
     * @var int
     */
    const SERIALIZE_TYPE_INT16 = 3;

    /**
     * @var int
     */
    const SERIALIZE_TYPE_INT8 = 4;

    /**
     * @var int
     */
    const SERIALIZE_TYPE_UINT64 = 5;

    /**
     * @var int
     */
    const SERIALIZE_TYPE_UINT32 = 6;

    /**
     * @var int
     */
    const SERIALIZE_TYPE_UINT16 = 7;

    /**
     * @var int
     */
    const SERIALIZE_TYPE_UINT8 = 8;

    /**
     * @var int
     */
    const SERIALIZE_TYPE_DOUBLE = 9;

    /**
     * @var int
     */
    const SERIALIZE_TYPE_STRING = 10;

    /**
     * @var int
     */
    const SERIALIZE_TYPE_BOOL = 11;

    /**
     * @var int
     */
    const SERIALIZE_TYPE_OBJECT = 12;

    /**
     * @var int
     */
    const SERIALIZE_TYPE_ARRAY = 13;

    /**
     * @var int
     */
    const SERIALIZE_FLAG_ARRAY = 0x80;

    /**
     * @return \Denpa\Levin\Types\Ubyte
     */
    public function getSerializeType() : Ubyte;
}
