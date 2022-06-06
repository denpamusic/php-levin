<?php

declare(strict_types=1);

namespace Denpa\Levin;

interface ConnectionInterface
{
    /**
     * @param callable $callback
     *
     * @return void
     */
    public function connect(callable $callback): void;

    /**
     * @param mixed $object
     *
     * @return void
     */
    public function read($object);

    /**
     * @param int $bytesize
     *
     * @return void
     */
    public function readBytes(int $bytesize);

    /**
     * @param mixed $object
     *
     * @return void
     */
    public function write($object): void;

    /**
     * @param string $bytes
     *
     * @return void
     */
    public function writeBytes(string $bytes): void;

    /**
     * @return void
     */
    public function isOpen(): bool;

    /**
     * @return void
     */
    public function close(): void;
}
