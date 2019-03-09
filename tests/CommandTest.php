<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests;

use Denpa\Levin\Command;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Types\Uint32;

class CommandTest extends TestCase
{
    /**
     * @var int
     */
    protected $commandBase = 0;

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

    /**
     * @param array $map
     *
     * @return void
     */
    protected function assertRequestMap(array $map = []) : void
    {
        $this->assertDataMap((new $this->classname())->request(), $map);
    }

    /**
     * @param array $map
     *
     * @return void
     */
    protected function assertResponseMap(array $map = []) : void
    {
        $this->assertDataMap((new $this->classname())->response(), $map);
    }

    /**
     * @param mixed $response
     * @param array $map
     *
     * @return void
     */
    protected function assertDataMap($response, array $map = []) : void
    {
        if (empty($map)) {
            $this->assertEmpty($response);

            return;
        }

        foreach ($response as $key => $entry) {
            if ($entry instanceof Section) {
                $this->assertDataMap($response[$key], $map[$key]);
                continue;
            }

            $this->assertInstanceOf(get_class($map[$key]), $response[$key]);
        }
    }

    /**
     * @param int $code
     *
     * @return void
     */
    protected function assertCommandCode(int $code) : void
    {
        $command = new $this->classname();
        $this->assertSame($this->commandBase + $code, $command->getCommandCode());
    }

    /**
     * @param array $vars
     *
     * @return void
     */
    protected function assertVars(array $vars = []) : void
    {
        $classVars = (array) (new $this->classname($vars));
        $this->assertEquals($classVars["\0*\0vars"], $vars);
    }
}
