<?php

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\Bytearray;
use Denpa\Levin\Types\BoostSerializable;

class BytearrayTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->bytearray = new Bytearray();
        $this->bytearray['foo'] = 'bar';
    }

    /**
     * @return void
     */
    public function testIsBigEndian() : void
    {
        $this->assertFalse($this->bytearray->isBigEndian());
    }

    /**
     * @return void
     */
    public function testGetByteSize() : void
    {
        $this->assertEquals(strlen($this->bytearray->toBinary()), $this->bytearray->getByteSize());
    }

    /**
     * @return void
     */
    public function testToHex() : void
    {
        $this->assertEquals(bin2hex($this->bytearray->toBinary()), $this->bytearray->toHex());
    }

    /**
     * @return void
     */
    public function testToBinary() : void
    {
        $this->assertEquals('bar', $this->bytearray->toBinary());
    }

    /**
     * @return void
     */
    public function testToString() : void
    {
        $this->assertEquals($this->bytearray->toBinary(), (string) $this->bytearray);
    }

    /**
     * @return void
     */
    public function testGetSerializeType() : void
    {
        $this->assertEquals(BoostSerializable::SERIALIZE_TYPE_ARRAY, $this->bytearray->getSerializeType()->toInt());
    }
}
