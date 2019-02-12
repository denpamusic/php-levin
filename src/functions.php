<?php

namespace Denpa\Levin;

use Denpa\Levin\Types\TypeInterface;

if (!function_exists('bytestring')) {
    /**
     * @param string $bytes
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function bytestring(string $bytes = '') : TypeInterface
    {
        return new Types\Bytestring($bytes);
    }
}

if (!function_exists('bytearray')) {
    /**
     * @param array $entries
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function bytearray(array $entries = []) : TypeInterface
    {
        return new Types\Bytearray($entries);
    }
}

if (!function_exists('boolean')) {
    /**
     * @param bool $bool
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function boolean(bool $bool = false) : TypeInterface
    {
        return new Types\Boolean($bool);
    }
}

if (!function_exists('varint')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function varint($int = 0) : TypeInterface
    {
        return new Types\VarInt($int);
    }
}

if (!function_exists('ubyte')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function ubyte($int = 0) : TypeInterface
    {
        return new Types\uByte($int);
    }
}

if (!function_exists('uint8le')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function uint8le($int = 0) : TypeInterface
    {
        return new Types\uInt8($int, TypeInterface::LE);
    }
}

if (!function_exists('int8le')) {
    /**
     * @var int|string
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function int8le($int = 0) : TypeInterface
    {
        return new Types\Int8($int, TypeInterface::LE);
    }
}

if (!function_exists('uint16le')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function uint16le($int = 0) : TypeInterface
    {
        return new Types\uInt16($int, TypeInterface::LE);
    }
}

if (!function_exists('int16le')) {
    /**
     * @var int|string
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function int16le($int = 0) : TypeInterface
    {
        return new Types\Int16($int, TypeInterface::LE);
    }
}

if (!function_exists('uint32le')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function uint32le($int = 0) : TypeInterface
    {
        return new Types\uInt32($int, TypeInterface::LE);
    }
}

if (!function_exists('int32le')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function int32le($int = 0) : TypeInterface
    {
        return new Types\Int32($int, TypeInterface::LE);
    }
}

if (!function_exists('uint64le')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function uint64le($int = 0) : TypeInterface
    {
        return new Types\uInt64($int, TypeInterface::LE);
    }
}

if (!function_exists('int64le')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function int64le($int = 0) : TypeInterface
    {
        return new Types\Int64($int, TypeInterface::LE);
    }
}

if (!function_exists('camel_case')) {
    /**
     * @param string $string
     *
     * @return string
     */
    function camel_case(string $string) : string
    {
        $string = str_replace('_', ' ', $string);

        return str_replace(' ', '', ucwords(strtolower($string)));
    }
}
