<?php

declare(strict_types=1);

namespace Denpa\Levin\Traits;

use ArrayIterator;

trait Arrayable
{
    /**
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->entries[$offset] = $value;
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->entries[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->entries[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->entries[$offset] ?? null;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->entries);
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->entries);
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->entries);
    }
}
