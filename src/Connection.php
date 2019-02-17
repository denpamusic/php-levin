<?php

namespace Denpa\Levin;

use Denpa\Levin\Exceptions\ConnectionException;
use Denpa\Levin\Types\TypeInterface;

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
     * @return void
     */
    public function __construct(string $host, $port, int $timeout = 5)
    {
        $this->socket = @fsockopen($host, (int) $port, $errno, $errstr, $timeout);

        if (!$this->socket) {
            throw new ConnectionException($errstr, $errno);
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
     * @param callable $callback
     *
     * @return void
     */
    public function listen(callable $callback) : void
    {
        if (!$this->isOpen()) {
            return;
        }

        while ($bucket = $this->read()) {
            if ($callback($bucket, $this) === false) {
                break;
            }
        }
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
     * @return mixed
     */
    public function readBytes(int $bytesize)
    {
        return fread($this->socket, $bytesize);
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
        fwrite($this->socket, $bytes);
    }

    /**
     * @return bool
     */
    public function eof() : bool
    {
        return feof($this->socket);
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
            fclose($this->socket);
            $this->open = false;
        }
    }
}
