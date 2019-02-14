<?php

namespace Denpa\Levin;

use Denpa\Levin\Section\Section;
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
        return new Types\Varint($int);
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
        return new Types\Ubyte($int);
    }
}

if (!function_exists('uint8')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function uint8($int = 0) : TypeInterface
    {
        return new Types\Uint8($int, TypeInterface::BE);
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
        return new Types\Uint8($int, TypeInterface::LE);
    }
}

if (!function_exists('int8')) {
    /**
     * @param int|string
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function int8($int = 0) : TypeInterface
    {
        return new Types\Int8($int, TypeInterface::BE);
    }
}

if (!function_exists('int8le')) {
    /**
     * @param int|string
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function int8le($int = 0) : TypeInterface
    {
        return new Types\Int8($int, TypeInterface::LE);
    }
}

if (!function_exists('uint16')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function uint16($int = 0) : TypeInterface
    {
        return new Types\Uint16($int, TypeInterface::BE);
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
        return new Types\Uint16($int, TypeInterface::LE);
    }
}

if (!function_exists('int16')) {
    /**
     * @param int|string
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function int16($int = 0) : TypeInterface
    {
        return new Types\Int16($int, TypeInterface::BE);
    }
}

if (!function_exists('int16le')) {
    /**
     * @param int|string
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function int16le($int = 0) : TypeInterface
    {
        return new Types\Int16($int, TypeInterface::LE);
    }
}

if (!function_exists('uint32')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function uint32($int = 0) : TypeInterface
    {
        return new Types\Uint32($int, TypeInterface::BE);
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
        return new Types\Uint32($int, TypeInterface::LE);
    }
}

if (!function_exists('int32')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function int32($int = 0) : TypeInterface
    {
        return new Types\Int32($int, TypeInterface::BE);
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

if (!function_exists('uint64')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function uint64($int = 0) : TypeInterface
    {
        return new Types\Uint64($int, TypeInterface::BE);
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
        return new Types\Uint64($int, TypeInterface::LE);
    }
}

if (!function_exists('int64')) {
    /**
     * @param int|string $int
     *
     * @return \Denpa\Levin\Types\TypeInterface
     */
    function int64($int = 0) : TypeInterface
    {
        return new Types\Int64($int, TypeInterface::BE);
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

if (!function_exists('section')) {
    /**
     * @param array $section
     *
     * @return \Denpa\Levin\Section\Section
     */
    function section(array $section = []) : Section
    {
        return new Section($section);
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

if (!function_exists('peer_id')) {
    /**
     * @param string $prefix
     *
     * @return \Denpa\Levin\Types\Uint64
     */
    function peer_id(string $prefix = '') : Types\Uint64
    {
        static $peerId = [];

        if (!isset($peerId[$prefix])) {
            $bin = hex2bin($prefix);

            if (strlen($bin) >= 8) {
                $bin = substr($bin, 0, 8);
            }

            $random = ($length = 8 - strlen($bin)) == 0 ?
                '' : random_bytes($length);

            $peerId[$prefix] = new Types\Uint64($bin.$random, TypeInterface::LE);
        }

        return $peerId[$prefix];
    }
}
