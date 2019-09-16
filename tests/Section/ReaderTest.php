<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Section;

use Denpa\Levin;
use Denpa\Levin\Connection;
use Denpa\Levin\Exceptions\SignatureMismatchException;
use Denpa\Levin\Exceptions\UnexpectedTypeException;
use Denpa\Levin\Section\Reader;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\BoostSerializable;
use Denpa\Levin\Types\Bytearray;
use Denpa\Levin\Types\Uint32;
use Denpa\Levin\Types\Uint8;
use Denpa\Levin\Types\Varint;

class ReaderTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->signatures = Levin\section()->getSignatures();
        $this->connection = $this->createMock(Connection::class);
        $this->reader = new FakeReader($this->connection);
    }

    /**
     * @return void
     */
    public function testRead() : void
    {
        $this->connection
            ->expects($this->exactly(4))
            ->method('read')
            ->withConsecutive(
                [$this->isInstanceOf(Uint32::class)],
                [$this->isInstanceOf(Uint32::class)],
                [$this->isInstanceOf(Uint8::class)],
                [$this->isInstanceOf(Varint::class)]
            )
            ->willReturnOnConsecutiveCalls(
                $this->signatures[0],
                $this->signatures[1],
                $this->signatures[2],
                new Varint(0)
            );

        $this->assertInstanceOf(Section::class, $this->reader->read());
    }

    /**
     * @return void
     */
    public function testReadWithSignatureMismatch() : void
    {
        $this->connection
            ->expects($this->exactly(3))
            ->method('read')
            ->withConsecutive(
                [$this->isInstanceOf(Uint32::class)],
                [$this->isInstanceOf(Uint32::class)],
                [$this->isInstanceOf(Uint8::class)]
            )
            ->willReturnOnConsecutiveCalls(
                $this->signatures[2],
                $this->signatures[1],
                $this->signatures[0]
            );

        $this->expectException(SignatureMismatchException::class);
        $this->expectExceptionMessage('Section signature mismatch');

        $this->reader->read();
    }

    /**
     * @return void
     */
    public function testGetName() : void
    {
        $this->connection
            ->expects($this->once())
            ->method('read')
            ->with($this->isInstanceOf(Uint8::class))
            ->willReturn(new Uint8(4));

        $this->connection
            ->expects($this->once())
            ->method('readBytes')
            ->with($this->equalTo(4))
            ->willReturn('test');

        $name = $this->reader->readName();
        $this->assertEquals('test', $name);
    }

    /**
     * @return void
     */
    public function testLoadEntries() : void
    {
        $this->connection
            ->expects($this->exactly(3))
            ->method('read')
            ->withConsecutive(
                [$this->isInstanceOf(Uint8::class)],
                [$this->isInstanceOf(Uint8::class)],
                [$this->isInstanceOf(Varint::class)]
            )
            ->willReturnOnConsecutiveCalls(
                new Uint8(Section::SERIALIZE_TYPE_ARRAY),
                new Uint8(Section::SERIALIZE_TYPE_UINT32),
                new Varint(0)
            );

        $entries = $this->reader->loadEntries();

        $this->assertInstanceOf(Bytearray::class, $entries);
        $this->assertSame(Section::SERIALIZE_TYPE_UINT32, $entries->getType()->toInt());
    }

    /**
     * @return void
     */
    public function testLoadEntriesWithIncorrectArrayTypeSequence() : void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Incorrect type sequence');

        $this->connection
            ->expects($this->exactly(2))
            ->method('read')
            ->withConsecutive(
                [$this->isInstanceOf(Uint8::class)],
                [$this->isInstanceOf(Uint8::class)]
            )
            ->willReturnOnConsecutiveCalls(
                new Uint8(Section::SERIALIZE_TYPE_ARRAY),
                new Uint8(Section::SERIALIZE_TYPE_UINT32 | Section::SERIALIZE_FLAG_ARRAY)
            );

        $this->reader->loadEntries();
    }

    /**
     * @return void
     */
    public function testReadArrayEntry() : void
    {
        $this->connection
            ->expects($this->exactly(3))
            ->method('read')
            ->withConsecutive(
                [$this->isInstanceOf(Varint::class)],
                [$this->isInstanceOf(Uint32::class)],
                [$this->isInstanceOf(Uint32::class)]
            )
            ->willReturnOnConsecutiveCalls(
                new Varint(2),
                new Uint32(39),
                new Uint32(40)
            );

        $type = Section::SERIALIZE_TYPE_UINT32 | Section::SERIALIZE_FLAG_ARRAY;

        $entries = $this->reader->readArrayEntry($type);
        $this->assertInstanceOf(Bytearray::class, $entries);
        $this->assertSame(39, $entries[0]->toInt());
        $this->assertSame(40, $entries[1]->toInt());
    }

    /**
     * @return void
     */
    public function testReadValueWithUnknownType() : void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Cannot unserialize unknown type [999]');

        $this->reader->readValue(999);
    }
}

class FakeReader extends Reader
{
    public function readSection() : Section
    {
        return parent::readSection();
    }

    public function readName() : string
    {
        return parent::readName();
    }

    public function loadEntries() : BoostSerializable
    {
        return parent::loadEntries();
    }

    public function readArrayEntry(int $type) : Bytearray
    {
        return parent::readArrayEntry($type);
    }

    public function readValue(int $type) : BoostSerializable
    {
        return parent::readValue($type);
    }
}
