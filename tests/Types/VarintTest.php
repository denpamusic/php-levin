<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Connection;
use Denpa\Levin\Exceptions\EntryTooLargeException;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\Uint8;
use Denpa\Levin\Types\Varint;

class VarintTest extends TestCase
{
    /**
     * @param int    $int
     * @param string $expect
     *
     * @return void
     *
     * @dataProvider intProvider
     */
    public function testToBinary(int $int, string $expect) : void
    {
        $this->assertEquals($expect, (new Varint($int))->toHex());
    }

    /**
     * @return void
     */
    public function testToBinaryTooLarge() : void
    {
        $this->expectException(EntryTooLargeException::class);
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
        ?int $second,
        string $bytes,
        int $expect
    ) : void {
        $connection = $this->createMock(Connection::class);

        $connection->expects($this->once())
            ->method('read')
            ->with($this->isInstanceOf(Uint8::class))
            ->willReturn(new Uint8($first));

        if (!is_null($second)) {
            $connection->expects($this->once())
                ->method('readBytes')
                ->with($this->equalTo($second))
                ->willReturn($bytes);
        }

        $varint = (new Varint())->read($connection);
        $this->assertEquals($expect, $varint->toInt());
    }

    /**
     * @return void
     */
    public function testGetTypeCode() : void
    {
        $this->assertEquals((new FakeVarInt())->getTypeCode(), '');
    }

    /**
     * @return array
     */
    public function bytesProvider() : array
    {
        return [
            [0x00, null, '', 0],
            [0x01, 1, "\xff", 16320],
            [0x02, 3, "\xff\xff\xff", 1073741760],
            [0x03, 7, "\x00\x00\x00\x00\x00\x00\x01", 18014398509481984],
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

class FakeVarInt extends Varint
{
    public function getTypeCode() : string
    {
        return parent::getTypeCode();
    }
}
