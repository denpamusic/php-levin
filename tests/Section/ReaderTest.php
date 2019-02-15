<?php

namespace Denpa\Levin\Tests\Section;

use Denpa\Levin;
use Denpa\Levin\Connection;
use Denpa\Levin\Exceptions\SignatureMismatchException;
use Denpa\Levin\Section\Reader;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\Ubyte;
use Denpa\Levin\Types\Uint32;
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
                [$this->isInstanceOf(Ubyte::class)],
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
                [$this->isInstanceOf(Ubyte::class)]
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
            ->with($this->isInstanceOf(Ubyte::class))
            ->willReturn(new Ubyte(4));

        $this->connection
            ->expects($this->once())
            ->method('readBytes')
            ->with($this->equalTo(4))
            ->willReturn('test');

        $name = $this->reader->readName();
        $this->assertEquals('test', $name);
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
}
