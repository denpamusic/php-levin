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
 * @param string $host
 * @param int    $port
 * @param mixed  &$errno
 * @param mixed  &$errstr
 * @param int    $timeout
 *
 * @return resource
 */
function fsockopen(
    string $host,
    int $port,
    &$errno,
    &$errstr,
    int $timeout
) {
    $errno = 101;
    $errstr = 'Test error message';
    global $fs;

    return fopen($fs->path("$host:$port"), 'r+');
}
