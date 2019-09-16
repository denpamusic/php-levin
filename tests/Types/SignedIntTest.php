<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\SignedInt;

class SignedIntTest extends TestCase
{
    /**
     * @return void
     */
    public function testMachineEndianness() : void
    {
        $signedint = $this->getMockForAbstractClass(SignedInt::class);

        $this->assertIsInt($signedint->machineEndianness());
    }

    /**
     * @return void
     *
     * @dataProvider binaryProvider
     */
    public function testToBinary($endianness, $result1, $result2) : void
    {
        $signedint = $this->getMockBuilder(SignedInt::class)
            ->setConstructorArgs([1, $endianness])
            ->setMethods(['machineEndianness', 'getTypeCode'])
            ->getMockForAbstractClass();

        $signedint->expects($this->exactly(2))
            ->method('getTypeCode')
            ->willReturn('S');

        $signedint->expects($this->exactly(2))
            ->method('machineEndianness')
            ->willReturnOnConsecutiveCalls(SignedInt::LE, SignedInt::BE);

        $this->assertEquals($result1, $signedint->toHex());
        $this->assertEquals($result2, $signedint->toHex());
    }

    /**
     * @return array
     */
    public function binaryProvider() : array
    {
        return [
            [SignedInt::LE, '0100', '0001'],
            [SignedInt::BE, '0001', '0100'],
        ];
    }
}
