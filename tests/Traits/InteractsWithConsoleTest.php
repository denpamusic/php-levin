<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Traits;

use Denpa\Levin\Console;
use Denpa\Levin\Tests\ConsoleTest;
use Denpa\Levin\Traits\InteractsWithConsole;

class InteractsWithConsoleTest extends ConsoleTest
{
    use InteractsWithConsole;

    /**
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();

        // disable console colors for tests
        $this
            ->console()
            ->target($this->getMockForConsoleTarget('STDOUT'));
    }

    /**
     * @return void
     */
    public function testConsole() : void
    {
        $this->console = null;

        $this->assertInstanceOf(Console::class, $this->console());
    }

    /**
     * @param string $expect
     * @param string $message
     * @param mixed  $args,...
     *
     * @return void
     *
     * @dataProvider messageProvider
     */
    public function testConsoleWithMessage(
        string $expect,
        string $message,
        ...$args
    ) : void {
        $this->console($message, ...$args);

        $this->assertConsoleTargetContains('STDOUT', $expect);
    }
}
