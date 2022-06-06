<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Traits;

use ArrayIterator;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Traits\Arrayable;

class ArrayableTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->array = $this->getMockForTrait(Arrayable::class);
        $this->array->offsetSet('foo', 'bar');
    }

    /**
     * @return void
     */
    public function testOffsetSetGet(): void
    {
        $this->assertEquals('bar', $this->array->offsetGet('foo'));
    }

    /**
     * @return void
     */
    public function testOffsetExists(): void
    {
        $this->assertTrue($this->array->offsetExists('foo'));
        $this->assertFalse($this->array->offsetExists('nonexistent'));
    }

    /**
     * @return void
     */
    public function testOffsetUnset(): void
    {
        $this->array->offsetUnset('foo');
        $this->assertFalse($this->array->offsetExists('foo'));
    }

    /**
     * @return void
     */
    public function testCount(): void
    {
        $this->assertEquals(1, $this->array->count());
        $this->array->offsetSet('bar', 'baz');
        $this->assertEquals(2, $this->array->count());
    }

    /**
     * @return void
     */
    public function testKeys(): void
    {
        $this->array->offsetSet('bar', 'baz');
        $this->assertEquals(['foo', 'bar'], $this->array->keys());
    }

    /**
     * @return void
     */
    public function testGetIterator(): void
    {
        $this->assertInstanceOf(ArrayIterator::class, $this->array->getIterator());
        $this->assertEquals('bar', $this->array->getIterator()->current());
    }
}
