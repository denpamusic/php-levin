<?php

namespace Denpa\Levin\Tests;

use Denpa\Levin\Bucket;
use Denpa\Levin\CommandFactory;
use VirtualFileSystem\FileSystem;

/**
 * @var \VirtualFileSystem\FileSystem
 */
$fs = null;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        global $fs;
        parent::setUp();

        $fs = $this->fs = new FileSystem();
    }

    /**
     * @param string $command
     * @param string $host
     * @param int    $port
     *
     * @return array
     */
    protected function createSocketMock(
        ?string $command,
        string $host = '127.0.0.1',
        int $port = 1000
    ) : array {
        $response = '';

        if (!is_null($command)) {
            $command = (new CommandFactory())->$command();
            $handshake = (new Bucket())->response($command);
            $response = $handshake->getHead().$handshake->getPayload()->toBinary();
        }

        file_put_contents($this->fs->path("$host:$port"), $response);

        return [$host, $port];
    }
}

namespace Denpa\Levin;

/**
 * Below are methods, used for mocking
 * socket operations via php-vfs.
 */

/**
 * @param int $domain
 * @param int $type
 * @param int $protocol
 *
 * @return null
 */
function socket_create(int $domain, int $type, int $protocol)
{
}

/**
 * @param resource $socket
 * @param int      $level
 * @param int      $optname
 * @param mixed    $optval
 *
 * @return bool
 */
function socket_set_option($socket, int $level, int $optname, $optval) : bool
{
    return true;
}

/**
 * @param resource $socket
 * @param string   $address
 * @param int      $port
 *
 * @return bool
 */
function socket_connect(&$socket, string $address, int $port = 0) : bool
{
    global $fs;

    $socket = fopen($fs->path("$address:$port"), 'r+');

    return $socket !== false;
}

/**
 * @param resource $socket
 * @param string   $buf
 * @param int      $len
 * @param int      $flags
 *
 * @return bool
 */
function socket_recv($socket, string &$buf, int $len, int $flags = 0)
{
    if (feof($socket)) {
        return false;
    }

    $buf = fread($socket, $len);

    return $len;
}

/**
 * @param resource $socket
 * @param string   $buf
 * @param int      $len
 * @param int      $flags
 *
 * @return bool
 */
function socket_send($socket, string $buf, int $len, int $flags = 0)
{
    fwrite($socket, $buf, $len);

    return $len;
}

/**
 * @param resource $socket
 *
 * @return void
 */
function socket_close($socket) : void
{
    fclose($socket);
}

namespace Denpa\Levin\Exceptions;

/**
 * @param resource $socket
 *
 * @return int
 */
function socket_last_error($socket) : int
{
    return 101;
}

/**
 * @param resource $socket
 *
 * @return void
 */
function socket_clear_error($socket) : void
{
    //
}

/**
 * @param int $errno
 *
 * @return void
 */
function socket_strerror(int $errno) : string
{
    if ($errno == 101) {
        return 'Test error message';
    }

    return '';
}
