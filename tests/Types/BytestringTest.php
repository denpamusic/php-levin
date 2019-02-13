<?php

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Connection;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\Varint;
use Denpa\Levin\Types\Bytestring;
use Denpa\Levin\Types\BoostSerializable;

class BytestringTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetSerializeType() : void
    {
        $this->assertEquals(BoostSerializable::SERIALIZE_TYPE_STRING, (new Bytestring)->getSerializeType()->toInt());
    }

    /**
     * @return void
     */
    public function testToBinary() : void
    {
        $this->assertEquals('foo', (new Bytestring('foo'))->toBinary());
    }

    /**
     * @return void
     */
    public function testGetRead() : void
    {
        $connection = $this->createMock(Connection::class);

        $connection->expects($this->once())
            ->method('read')
            ->with($this->isInstanceOf(Varint::class))
            ->willReturn(new Varint(3));

        $connection->expects($this->once())
            ->method('readBytes')
            ->with($this->equalTo(3))
            ->willReturn('foo');

        $bytestring = (new Bytestring)->read($connection);

        $this->assertEquals('foo', $bytestring->getValue());
    }
}
