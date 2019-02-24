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
    function bytearray(array $entries = [], $type = null) : TypeInterface
    {
        return new Types\Bytearray($entries, $type);
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

if (!function_exists('request')) {
    /**
     * Shortcut for creating the request bucket.
     *
     * @param string $command
     * @param array  $args
     *
     * @return \Denpa\Levin\Bucket
     */
    function request(string $command, array $args = []) : Bucket
    {
        return (new Bucket())->request((new CommandFactory())->$command($args));
    }
}

if (!function_exists('response')) {
    /**
     * Shortcut for creating the response bucket.
     *
     * @param string $command
     * @param array  $args
     *
     * @return \Denpa\Levin\Bucket
     */
    function response(string $command, array $args = []) : Bucket
    {
        return (new Bucket())->response((new CommandFactory())->$command($args));
    }
}

if (!function_exists('notification')) {
    /**
     * Shortcut for creating the notification bucket.
     *
     * @param string $command
     * @param array  $args
     *
     * @return \Denpa\Levin\Bucket
     */
    function notification(string $command, array $args = []) : Bucket
    {
        return (new Bucket())->notification((new CommandFactory())->$command($args));
    }
}

if (!function_exists('connection')) {
    /**
     * Shortcut for opening levin connection.
     *
     * @param string $address
     * @param mixed  $port
     * @param array  $vars
     *
     * @return \Denpa\Levin\ConnectionInterface
     */
    function connection(string $address, $port, array $vars = []) : ConnectionInterface
    {
        $connection = new Connection($address, $port);
        $connection->write(request('handshake', $vars));

        return $connection;
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
        $string = str_replace('_', ' ', ltrim(snake_case($string), '_'));

        return str_replace(' ', '', lcfirst(ucwords(strtolower($string))));
    }
}

if (!function_exists('snake_case')) {
    /**
     * @param string $string
     *
     * @return string
     *
     * @copyright 2016 Syone
     *
     * @link https://stackoverflow.com/a/35719689/10405250 Answer on StackOverflow.
     */
    function snake_case(string $string) : string
    {
        $string = preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string);

        return strtolower($string);
    }
}

if (!function_exists('peer_id')) {
    /**
     * Creates peer id with certain prefix.
     *
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
