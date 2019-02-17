<?php

namespace Denpa\Levin\Tests;

use Denpa\Levin\Bucket;
use Denpa\Levin\Connection;
use Denpa\Levin\Requests\Handshake;
use Denpa\Levin\Types\Uint64;
use Denpa\Levin\Exceptions\ConnectionException;
use VirtualFileSystem\FileSystem;

/**
 * @var \VirtualFileSystem\FileSystem
 */
$fs = null;

class ConnectionTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        global $fs;
        parent::setUp();

        $this->fs = new FileSystem();

        if (is_null($fs)) {
            $fs = $this->fs;
        }
    }

    /**
     * @return void
     */
    public function testListen() : void
    {
        $handshake = (new Bucket())->response(new Handshake());
        $response = $handshake->head().$handshake->payload()->toBinary();
        file_put_contents($this->fs->path('127.0.0.1:1000'), $response);

        $connection = new Connection('127.0.0.1', 1000);
        $connection->listen(function ($bucket, $connection) {
            $this->assertInstanceOf(Bucket::class, $bucket);
            $this->assertInstanceOf(Connection::class, $connection);
            $this->assertTrue($bucket->is('handshake'));
            
            return false;
        });
    }
    
    /**
     * @return void
     */
    public function testRead() : void
    {
        $handshake = (new Bucket())->response(new Handshake());
        $response = $handshake->head().$handshake->payload()->toBinary();
        file_put_contents($this->fs->path('127.0.0.1:1000'), $response);
        
        $uint64 = (new Connection('127.0.0.1', 1000))->read(new Uint64());
        $this->assertInstanceOf(Uint64::class, $uint64);
        $this->assertEquals(Bucket::LEVIN_SIGNATURE, $uint64->toInt());
    }
    
    /**
     * @return void
     */
    public function testReadWithConnectionException() : void
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage('Test error message');
        
        new Connection('127.0.0.1', 1001);
    }

    /**
     * @return void
     */
    public function testWrite() : void
    {
        $handshake = (new Bucket())->response(new Handshake());
        $connection = new Connection('127.0.0.1', 1000);
        $connection->write($handshake);
        $connection->close();
        
        // pointer resets after connection will be reopened due to "r+" mode
        // so we should be able to read the bucket, that we just wrote
        $bucket = (new Connection('127.0.0.1', 1000))->read(new Bucket());
        $this->assertInstanceOf(Bucket::class, $bucket);
        $this->assertTrue($bucket->is('handshake'));
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
