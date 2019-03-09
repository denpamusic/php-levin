<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Traits;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Traits\ConsoleColor;
use InvalidArgumentException;

class ConsoleColorTest extends TestCase
{
    use ConsoleColor;

    /**
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->enableColors();
    }

    /**
     * @return void
     */
    public function testColor() : void
    {
        $this->color('regular-black');

        $this->assertEquals('0;30', $this->color);
    }

    /**
     * @return void
     */
    public function testBackground() : void
    {
        $this->background('black');

        $this->assertEquals('40', $this->background);
    }

    /**
     * @param string $method
     * @param string $color
     * @param string $background
     *
     * @return void
     *
     * @dataProvider templateMethodsProvider
     */
    public function testTemplates(
        string $method,
        string $color = '',
        string $background = ''
    ) : void {
        $mock = $this->getMockBuilder(ConsoleColor::class)
            ->disableOriginalConstructor()
            ->setMethods(['line', 'resetColors', 'color', 'background'])
            ->getMockForTrait();

        $mock
            ->expects($this->exactly(2))
            ->method('resetColors')
            ->willReturnSelf();

        if ($color != '') {
            $mock
                ->expects($this->once())
                ->method('color')
                ->with($color)
                ->willReturnSelf();
        }

        if ($background != '') {
            $mock
                ->expects($this->once())
                ->method('background')
                ->with($background)
                ->willReturnSelf();
        }

        $mock
            ->expects($this->once())
            ->method('line')
            ->with('foo')
            ->willReturnSelf();

        $mock->$method('foo');
    }

    /**
     * @return array
     */
    public function templateMethodsProvider() : array
    {
        return [
            ['error', 'white', 'red'],
            ['warning', 'black', 'yellow'],
            ['info', 'bright-green'],
        ];
    }

    /**
     * @return void
     */
    public function testResetColors() : void
    {
        $this->color('regular-black')->background('black');
        $this->resetColors();

        $this->assertFalse(isset($this->color));
        $this->assertFalse(isset($this->background));
    }

    /**
     * @return void
     */
    public function testDisableColors() : void
    {
        $this->disableColors();

        $this->assertTrue($this->colorDisabled);
    }

    /**
     * @return void
     */
    public function testEnableColors() : void
    {
        $this->enableColors();

        $this->assertFalse($this->colorDisabled);
    }

    /**
     * @return void
     */
    public function testColorize() : void
    {
        $this->color('regular-black')->background('black');

        $output = $this->colorize('foo');

        $this->assertEquals("\e[0;30m\e[40mfoo\e[0m", $output);
    }

    /**
     * @return void
     */
    public function testParseColorString() : void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid color [foo]');

        $this->parseColorString('foo');
    }
}
