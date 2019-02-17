<?php

namespace Denpa\Levin\Tests;

use Denpa\Levin;
use Denpa\Levin\Bucket;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Types\Boolean;
use Denpa\Levin\Types\Bytearray;
use Denpa\Levin\Types\Bytestring;
use Denpa\Levin\Types\Int16;
use Denpa\Levin\Types\Int32;
use Denpa\Levin\Types\Int64;
use Denpa\Levin\Types\Int8;
use Denpa\Levin\Types\Uint16;
use Denpa\Levin\Types\Uint32;
use Denpa\Levin\Types\Uint64;
use Denpa\Levin\Types\Uint8;
use Denpa\Levin\Types\Varint;

class FunctionsTest extends TestCase
{
    /**
     * @return void
     */
    public function testBytestring() : void
    {
        $this->assertInstanceOf(Bytestring::class, Levin\bytestring('test'));
    }

    /**
     * @return void
     */
    public function testBytearray() : void
    {
        $this->assertInstanceOf(Bytearray::class, Levin\bytearray([
            Levin\uint16le(1),
        ]));
    }

    /**
     * @return void
     */
    public function testBoolean() : void
    {
        $this->assertInstanceOf(Boolean::class, Levin\boolean(true));
    }

    /**
     * @return void
     */
    public function testVarint() : void
    {
        $this->assertInstanceOf(Varint::class, Levin\varint(10));
    }

    /**
     * @return void
     */
    public function testUint8() : void
    {
        $this->assertInstanceOf(Uint8::class, Levin\uint8(1));
        $this->assertTrue(Levin\uint8(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testUint8le() : void
    {
        $this->assertInstanceOf(Uint8::class, Levin\uint8le(1));
        $this->assertFalse(Levin\uint8le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testInt8() : void
    {
        $this->assertInstanceOf(Int8::class, Levin\int8(1));
        $this->assertTrue(Levin\int8(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testInt8le() : void
    {
        $this->assertInstanceOf(Int8::class, Levin\int8le(1));
        $this->assertFalse(Levin\int8le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testUint16() : void
    {
        $this->assertInstanceOf(Uint16::class, Levin\uint16(1));
        $this->assertTrue(Levin\uint16(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testUint16le() : void
    {
        $this->assertInstanceOf(Uint16::class, Levin\uint16le(1));
        $this->assertFalse(Levin\uint16le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testInt16() : void
    {
        $this->assertInstanceOf(Int16::class, Levin\int16(1));
        $this->assertTrue(Levin\int16(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testInt16le() : void
    {
        $this->assertInstanceOf(Int16::class, Levin\int16le(1));
        $this->assertFalse(Levin\int16le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testUint32() : void
    {
        $this->assertInstanceOf(Uint32::class, Levin\uint32(1));
        $this->assertTrue(Levin\uint32(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testUint32le() : void
    {
        $this->assertInstanceOf(Uint32::class, Levin\uint32le(1));
        $this->assertFalse(Levin\uint32le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testInt32() : void
    {
        $this->assertInstanceOf(Int32::class, Levin\int32(1));
        $this->assertTrue(Levin\int32(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testInt32le() : void
    {
        $this->assertInstanceOf(Int32::class, Levin\int32le(1));
        $this->assertFalse(Levin\int32le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testUint64() : void
    {
        $this->assertInstanceOf(Uint64::class, Levin\uint64(1));
        $this->assertTrue(Levin\uint64(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testUint64le() : void
    {
        $this->assertInstanceOf(Uint64::class, Levin\uint64le(1));
        $this->assertFalse(Levin\uint64le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testInt64() : void
    {
        $this->assertInstanceOf(Int64::class, Levin\int64(1));
        $this->assertTrue(Levin\int64(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testInt64le() : void
    {
        $this->assertInstanceOf(Int64::class, Levin\int64le(1));
        $this->assertFalse(Levin\int64le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testSection() : void
    {
        $this->assertInstanceOf(Section::class, Levin\section());

        $uint16 = Levin\uint16(1);
        $this->assertEquals($uint16, Levin\Section(['foo' => $uint16])['foo']);
    }

    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertInstanceOf(Bucket::class, Levin\request('ping'));
        $this->assertTrue(Levin\request('ping')->isRequest('ping'));
    }

    /**
     * @return void
     */
    public function testResponse() : void
    {
        $this->assertInstanceOf(Bucket::class, Levin\response('ping'));
        $this->assertTrue(Levin\response('ping')->isResponse('ping'));
    }

    /**
     * @return void
     */
    public function testCamelCase() : void
    {
        $this->assertEquals('TestCamelcase', Levin\camel_case('teSt_cAMElcase'));
    }

    /**
     * @return void
     */
    public function testPeerId() : void
    {
        $this->assertInstanceOf(Uint64::class, Levin\peer_id());
        $this->assertEquals(Levin\peer_id()->toHex(), Levin\peer_id()->toHex());
        $this->assertEquals('beef', substr(Levin\peer_id('beef')->toHex(), 0, 4));
        $this->assertEquals(Levin\peer_id('beef')->toHex(), Levin\peer_id('beef')->toHex());

        // prefix overflow
        $this->assertEquals('feeddeabbeefcafe', Levin\peer_id('feeddeabbeefcafef00d')->toHex());
    }
}
