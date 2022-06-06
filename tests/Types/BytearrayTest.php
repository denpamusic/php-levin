<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Exceptions\UnexpectedTypeException;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\BoostSerializable;
use Denpa\Levin\Types\Bytearray;
use Denpa\Levin\Types\Bytestring;
use Denpa\Levin\Types\Uint8;

class BytearrayTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->bytearray = new Bytearray();
        $this->bytearray[] = new Bytestring('bar');
    }

    /**
     * @return void
     */
    public function testCreateWithIllegalType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Array entries must be serializable');
        $bytearray = new Bytearray(['fail']);
    }

    /**
     * @return void
     */
    public function testCreateWithMultipleTypes(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Array entries must be of the same type');

        $bytearray = new Bytearray([
            new Bytestring('fail'),
            new Uint8(1),
        ]);
    }

    /**
     * @return void
     */
    public function offsetSet(): void
    {
        $this->bytearray->offsetSet(null, new Bytestring('test'));
        $this->assertEquals('test', $this->bytearray[1]->getValue());
    }

    /**
     * @return void
     */
    public function testOffsetSetWithIllegalType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Array entries must be serializable');
        $this->bytearray->offsetSet(null, 'fail');
    }

    /**
     * @return void
     */
    public function testOffsetSetWithMultipleTypes(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Array entries must be of the same type');

        $this->bytearray->offsetSet(null, new Uint8(1));
    }

    /**
     * @return void
     */
    public function testIsBigEndian(): void
    {
        $this->assertFalse($this->bytearray->isBigEndian());
    }

    /**
     * @return void
     */
    public function testGetByteSize(): void
    {
        $this->assertEquals(strlen($this->bytearray->toBinary()), $this->bytearray->getByteSize());
    }

    /**
     * @return void
     */
    public function testToHex(): void
    {
        $this->assertEquals(bin2hex($this->bytearray->toBinary()), $this->bytearray->toHex());
    }

    /**
     * @return void
     */
    public function testToBinary(): void
    {
        $this->assertEquals("\x04\x0c\x62\x61\x72", $this->bytearray->toBinary());
    }

    /**
     * @return void
     */
    public function testToString(): void
    {
        $this->assertEquals($this->bytearray->toBinary(), (string) $this->bytearray);
    }

    /**
     * @return void
     */
    public function testGetSerializeType(): void
    {
        $type = $this
            ->bytearray
            ->getSerializeType()
            ->and(~BoostSerializable::SERIALIZE_FLAG_ARRAY);

        $this->assertEquals(BoostSerializable::SERIALIZE_TYPE_STRING, $type->toInt());
    }

    /**
     * @return void
     */
    public function testGetSerializeTypeWithEmptyArray(): void
    {
        $type = (new Bytearray([]))->getSerializeType();

        $this->assertEquals(BoostSerializable::SERIALIZE_TYPE_ARRAY, $type->toInt());
    }
}
