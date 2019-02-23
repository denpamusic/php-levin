<?php

namespace Denpa\Levin\Tests;

use Denpa\Levin\Bucket;
use Denpa\Levin\Connection;
use Denpa\Levin\Exceptions\ConnectionException;
use Denpa\Levin\Exceptions\UnpackException;
use Denpa\Levin\Requests\Handshake;
use Denpa\Levin\Types\Uint64;
use Throwable;

class ConnectionTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->socket = $this->createSocketMock('handshake');
    }

    /**
     * @return void
     */
    public function testConnect() : void
    {
        $connection = new Connection(...$this->socket);
        $connection->connect(function ($bucket, $connection) {
            $this->assertInstanceOf(Bucket::class, $bucket);
            $this->assertInstanceOf(Connection::class, $connection);
            $this->assertTrue($bucket->is('handshake'));

            return false;
        });
    }

    /**
     * @return void
     */
    public function testConnectWithFailure() : void
    {
        $socket = $this->createSocketMock(null, '127.0.0.2');
        $connection = new Connection(...$socket);
        $connection->writeBytes('~');
        $connection->close();

        $connection = new Connection(...$socket);
        $connection->connect(null, function (Throwable $exception) {
            $this->assertEquals(
                'Failed to unpack binary data [~]',
                $exception->getMessage()
            );
            $this->assertInstanceOf(UnpackException::class, $exception);
        });
    }

    /**
     * @return void
     */
    public function testConnectWithNullCallback() : void
    {
        $connection = new Connection(...$this->socket);
        $connection->connect(null);

        $this->addToAssertionCount(1);  // does not throw an exception
    }

    /**
     * @return void
     */
    public function testConnectOnClosedConnection() : void
    {
        $connection = new Connection(...$this->socket);
        $connection->close();

        $run = false;
        $connection->connect(function ($bucket) use ($run) {
            $run = true;
        });

        $this->assertFalse($run, 'Listen function run on closed connection.');
    }

    /**
     * @return void
     */
    public function testRead() : void
    {
        $connection = new Connection(...$this->socket);
        $uint64 = $connection->read(new Uint64());
        $this->assertInstanceOf(Uint64::class, $uint64);
        $this->assertEquals(Bucket::LEVIN_SIGNATURE, $uint64->toInt());

        $size = (new Uint64())->getByteSize();
        $this->assertEquals(
            "\x08\x01\x00\x00\x00\x00\x00\x00",
            $connection->read($size)
        );
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
        $socket = $this->createSocketMock(null, '127.0.0.2');

        $handshake = (new Bucket())->response(new Handshake());

        $connection = new Connection(...$socket);
        $connection->write($handshake);
        $connection->close();

        // pointer resets after connection will be reopened due to "r+" mode
        // so we should be able to read the bucket, that we just wrote
        $bucket = (new Connection(...$socket))->read(new Bucket());
        $this->assertInstanceOf(Bucket::class, $bucket);
        $this->assertTrue($bucket->is('handshake'));
    }
}
