<?php

namespace Denpa\Levin\Tests;

use Denpa\Levin\Command;
use Denpa\Levin\Types\Uint32;

class CommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetCommand() : void
    {
        $command = $this->getMockForAbstractClass(Command::class);

        $command->expects($this->once())
            ->method('getCommandCode')
            ->willReturn(1001);

        $int = $command->getCommand();
        $this->assertInstanceOf(Uint32::class, $int);
        $this->assertEquals(1001, $int->toInt());
    }
}