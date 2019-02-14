<?php

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\Type;
use UnexpectedValueException;

class TypeTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->type = $this->getMockForAbstractClass(Type::class, [1, Type::BE]);
    }

    /**
     * @return void
     */
    public function testIsBigEndian()
    {
        $type = $this->getMockForAbstractClass(Type::class, [1, Type::BE]);
        $this->assertTrue($type->isBigEndian());
        $type = $this->getMockForAbstractClass(Type::class, [1, Type::LE]);
        $this->assertFalse($type->isBigEndian());
    }

    /**
     * @return void
     */
    public function testToBinary()
    {
        $this->type->expects($this->once())
            ->method('getTypeCode')
            ->willReturn('S');

        $this->assertEquals("\x01\x00", $this->type->toBinary());
    }

    /**
     * @return void
     */
    public function testToBinaryWithInvalidData()
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Failed to unpack binary data [&]');

        $type = $this->getMockForAbstractClass(Type::class, ['&']);

        $type->expects($this->once())
            ->method('getTypeCode')
            ->willReturn('S');

        $type->toBinary();
    }

    /**
     * @return void
     */
    public function testToHex()
    {
        $this->type->expects($this->once())
            ->method('getTypeCode')
            ->willReturn('S');

        $this->assertEquals(bin2hex("\x01\x00"), $this->type->toHex());
    }

    /**
     * @return void
     */
    public function testToInt()
    {
        $this->assertSame(1, $this->type->toInt());
    }

    /**
     * @return void
     */
    public function testToString()
    {
        $this->type->expects($this->any())
            ->method('getTypeCode')
            ->willReturn('S');

        $this->assertSame($this->type->toBinary(), (string) $this->type);
    }

    /**
     * @return void
     */
    public function testGetByteSize()
    {
        $this->type->expects($this->any())
            ->method('getTypeCode')
            ->willReturn('S');

        $this->assertEquals(2, $this->type->getByteSize());
    }

    /**
     * @return void
     */
    public function testCount()
    {
        $this->type->expects($this->any())
            ->method('getTypeCode')
            ->willReturn('S');

        $this->assertEquals(2, count($this->type));
    }

    /**
     * @return void
     */
    public function testGetValue()
    {
        $this->assertEquals(1, $this->type->getValue());
    }

    /**
     * @return void
     */
    public function testOr() : void
    {
        $type = $this->getMockForAbstractClass(Type::class, [10, Type::BE]);
        $this->assertEquals(10 | 8, $type->or(8)->toInt());
    }

    /**
     * @return void
     */
    public function testAnd() : void
    {
        $type = $this->getMockForAbstractClass(Type::class, [10, Type::BE]);
        $this->assertEquals(10 & 8, $type->and(8)->toInt());
    }
}
