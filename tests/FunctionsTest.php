<?php

namespace Denpa\Levin\Tests;

use Denpa\Levin;
use Denpa\Levin\Bucket;
use Denpa\Levin\Connection;
use Denpa\Levin\Requests\Handshake;
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
    public function testConnection() : void
    {
        $socket = $this->createSocketMock(null);

        $connection = Levin\connection(...$socket);
        $this->assertInstanceOf(Connection::class, $connection);
        $connection->close();

        // pointer resets after connection will be reopened due to "r+" mode
        // so we should be able to read the bucket, that we just wrote
        $connection = new Connection(...$socket);

        $bucket = $connection->read();
        $this->assertInstanceOf(Handshake::class, $bucket->getCommand());
        $this->assertEquals(
            Bucket::LEVIN_PACKET_REQUEST,
            $bucket->getFlags()->toInt()
        );
    }

    /**
     * @return void
     *
     * @dataProvider camelCaseProvider
     */
    public function testCamelCase(string $string, string $expected) : void
    {
        $this->assertEquals($expected, Levin\camel_case($string));
    }

    /**
     * @return void
     *
     * @dataProvider snakeCaseProvider
     */
    public function testSnakeCase(string $string, string $expected) : void
    {
        $this->assertEquals($expected, Levin\snake_case($string));
    }

    /**
     * @return array
     */
    public function camelCaseProvider() : array
    {
        return [
            ['test_camel_case', 'testCamelCase'],
            ['test__camel_case', 'testCamelCase'],
            ['testCamelCase', 'testCamelCase'],
            ['TESTCamelCase123', 'testCamelCase123'],
            ['_test_camel_case', 'testCamelCase'],
            ['123testCAMELCase', '123testCamelCase'],
        ];
    }

    /**
     * @return array
     */
    public function snakeCaseProvider() : array
    {
        return [
            ['testSnakeCase', 'test_snake_case'],
            ['TestSnakeCase', 'test_snake_case'],
            ['test_snake_case', 'test_snake_case'],
            ['TESTSnakeCASE', 'test_snake_case'],
            ['__TestSnakeCase123', '__test_snake_case123'],
            ['123testCAMELCase', '123test_camel_case'],
        ];
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
