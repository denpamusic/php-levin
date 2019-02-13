<?php

namespace Denpa\Levin\Tests\Types;

use UnexpectedValueException;
use Denpa\Levin\Connection;
use Denpa\Levin\Types\Ubyte;
use Denpa\Levin\Types\Varint;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\BoostSerializable;

class VarintTest extends TestCase
{
    /**
     * @param int $int
     * @param string $expected
     *
     * @return void
     *
     * @dataProvider intProvider
     */
    public function testToBinary(int $int, string $expected) : void
    {
        $this->assertEquals($expected, (new Varint($int))->toHex());
    }

    /**
     * @return void
     */
    public function testToBinaryTooLarge() : void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('VarInt is too large [> 4611686018427387903]');
        (new Varint(4611686018427387904))->toBinary();
    }

    /**
     * @param int    $first
     * @param int    $second
     * @param string $bytes
     * @param int    $expect
     *
     * @return void
     *
     * @dataProvider bytesProvider
     */
    public function testRead(
        int $first,
        int $second,
        string $bytes,
        int $expected
    ) : void
    {
        $connection = $this->createMock(Connection::class);

        $connection->expects($this->once())
            ->method('read')
            ->with($this->isInstanceOf(Ubyte::class))
            ->willReturn(new Ubyte($first));

        $connection->expects($this->once())
            ->method('readBytes')
            ->with($this->equalTo($second))
            ->willReturn($bytes);

        $varint = (new VarInt())->read($connection);
        $this->assertEquals($expected, $varint->toInt());
    }

    /**
     * @return array
     */
    public function bytesProvider() : array
    {
        return [
            [1, 1, "\xff", 16320],
            [2, 3, "\xff\xff\xff", 1073741760],
            [3, 7, "\x00\x00\x00\x00\x00\x00\x01", 18014398509481984],
        ];
    }

    /**
     * @return array
     */
    public function intProvider() : array
    {
        return [
            [62, 'f8'],
            [16382, 'f9ff'],
            [1073741822, 'faffffff'],
            [1073741825, '0700000001000000'],
        ];
    }
}