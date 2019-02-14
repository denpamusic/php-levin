<?php

namespace Denpa\Levin\Tests;

use Denpa\Levin;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Types\Boolean;
use Denpa\Levin\Types\Bytearray;
use Denpa\Levin\Types\Bytestring;
use Denpa\Levin\Types\Int16;
use Denpa\Levin\Types\Int32;
use Denpa\Levin\Types\Int64;
use Denpa\Levin\Types\Int8;
use Denpa\Levin\Types\Ubyte;
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
    public function testBytestring()
    {
        $this->assertInstanceOf(Bytestring::class, Levin\bytestring('test'));
    }

    /**
     * @return void
     */
    public function testBytearray()
    {
        $this->assertInstanceOf(Bytearray::class, Levin\bytearray([
            Levin\uint16le(1),
        ]));
    }

    /**
     * @return void
     */
    public function testBoolean()
    {
        $this->assertInstanceOf(Boolean::class, Levin\boolean(true));
    }

    /**
     * @return void
     */
    public function testVarint()
    {
        $this->assertInstanceOf(Varint::class, Levin\varint(10));
    }

    /**
     * @return void
     */
    public function testUbyte()
    {
        $this->assertInstanceOf(Ubyte::class, Levin\ubyte(1));
    }

    /**
     * @return void
     */
    public function testUint8le()
    {
        $this->assertInstanceOf(Uint8::class, Levin\uint8le(1));
        $this->assertFalse(Levin\uint8le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testInt8le()
    {
        $this->assertInstanceOf(Int8::class, Levin\int8le(1));
        $this->assertFalse(Levin\int8le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testUint16le()
    {
        $this->assertInstanceOf(Uint16::class, Levin\uint16le(1));
        $this->assertFalse(Levin\uint16le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testInt16le()
    {
        $this->assertInstanceOf(Int16::class, Levin\int16le(1));
        $this->assertFalse(Levin\int16le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testUint32le()
    {
        $this->assertInstanceOf(Uint32::class, Levin\uint32le(1));
        $this->assertFalse(Levin\uint32le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testInt32le()
    {
        $this->assertInstanceOf(Int32::class, Levin\int32le(1));
        $this->assertFalse(Levin\int32le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testUint64le()
    {
        $this->assertInstanceOf(Uint64::class, Levin\uint64le(1));
        $this->assertFalse(Levin\uint64le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testInt64le()
    {
        $this->assertInstanceOf(Int64::class, Levin\int64le(1));
        $this->assertFalse(Levin\int64le(1)->isBigEndian());
    }

    /**
     * @return void
     */
    public function testSection()
    {
        $this->assertInstanceOf(Section::class, Levin\section());
    }

    /**
     * @return void
     */
    public function testCamelCase()
    {
        $this->assertEquals('TestCamelcase', Levin\camel_case('teSt_cAMElcase'));
    }

    /**
     * @return void
     */
    public function testPeerId()
    {
        $this->assertInstanceOf(Uint64::class, Levin\peer_id());
        $this->assertEquals(Levin\peer_id()->toHex(), Levin\peer_id()->toHex());
        $this->assertEquals('beef', substr(Levin\peer_id('beef')->toHex(), 0, 4));
        $this->assertEquals(Levin\peer_id('beef')->toHex(), Levin\peer_id('beef')->toHex());

        // prefix overflow
        $this->assertEquals('feeddeabbeefcafe', Levin\peer_id('feeddeabbeefcafef00d')->toHex());
    }
}
