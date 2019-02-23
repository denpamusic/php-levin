<?php

namespace Denpa\Levin;

use Denpa\Levin\Exceptions\ConnectionException;
use Denpa\Levin\Exceptions\ConnectionTerminatedException;
use Denpa\Levin\Types\TypeInterface;
use Throwable;

class Connection implements ConnectionInterface
{
    /**
     * @var resource
     */
    protected $socket;

    /**
     * @var bool
     */
    protected $open = false;

    /**
     * @param string $host
     * @param mixed  $port
     *
     * @throws \Denpa\Levin\Exceptions\ConnectionException
     *
     * @return void
     */
    public function __construct(string $host, $port, int $timeout = 5)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_option($this->socket, SOL_SOCKET, SO_KEEPALIVE, 1);
        @socket_connect($this->socket, $host, (int) $port);

        if ($this->socket === false) {
            throw new ConnectionException($this->socket);
        }

        $this->open = true;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @param callable|null $success
     * @param callable|null $failure
     *
     * @return void
     */
    public function connect(?callable $success, ?callable $failure = null) : void
    {
        if (!$this->isOpen()) {
            return;
        }

        $bucket = null;

        do {
            try {
                $bucket = $this->read();
            } catch (Throwable $exception) {
                $this->onFailure($failure, $exception);
                continue;
            }

            if ($this->onSuccess($success, $bucket) === false) {
                break;
            }
        } while ($bucket);
    }

    /**
     * @param mixed $object
     *
     * @return mixed
     */
    public function read($object = null)
    {
        if (is_null($object)) {
            $object = new Bucket();
        }

        if (method_exists($object, 'read')) {
            return $object->read($this);
        }

        if ($object instanceof TypeInterface) {
            return new $object($this->readBytes($object->getByteSize()), $object::LE);
        }

        return $this->readBytes($object);
    }

    /**
     * @param int $bytesize
     *
     * @return string
     */
    public function readBytes(int $bytesize) : string
    {
        $buffer = '';
        $bytes = socket_recv($this->socket, $buffer, $bytesize, MSG_WAITALL);

        if ($bytes === false || $bytes === 0) {
            // lost connection
            $this->close();

            throw new ConnectionTerminatedException();
        }

        return $buffer;
    }

    /**
     * @param mixed $object
     *
     * @return void
     */
    public function write($object) : void
    {
        if (method_exists($object, 'write')) {
            $object->write($this);

            return;
        }

        $this->writeBytes($object);
    }

    /**
     * @param string $bytes
     *
     * @return void
     */
    public function writeBytes(string $bytes) : void
    {
        socket_send($this->socket, $bytes, strlen($bytes), 0);
    }

    /**
     * @return bool
     */
    public function isOpen() : bool
    {
        return $this->open;
    }

    /**
     * @return void
     */
    public function close() : void
    {
        if ($this->isOpen()) {
            socket_close($this->socket);
            $this->open = false;
        }
    }

    /**
     * @param callable|null $failure
     * @param \Throwable    $exception
     *
     * @return void
     */
    protected function onFailure(?callable $failure, Throwable $exception) : void
    {
        if (is_callable($failure)) {
            $failure($exception);
        }
    }

    /**
     * @param callable|null            $success
     * @param \Denpa\Levin\Bucket|null $bucket
     *
     * @return mixed
     */
    protected function onSuccess(?callable $success, ?Bucket $bucket)
    {
        if (!is_null($bucket) && is_callable($success)) {
            return $success($bucket, $this);
        }
    }
}
