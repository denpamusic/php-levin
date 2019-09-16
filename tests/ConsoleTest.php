<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests;

use Denpa\Levin\Bucket;
use Denpa\Levin\Console;
use Denpa\Levin\Requests\Handshake;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Types\Bytearray;
use Denpa\Levin\Types\Bytestring;
use Denpa\Levin\Types\Uint64;

class ConsoleTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->console = (new Console())
            ->target($this->getMockForConsoleTarget('STDOUT'));
    }

    /**
     * @return void
     */
    public function tearDown() : void
    {
        parent::tearDown();

        // clear file after test
        ftruncate($this->getMockForConsoleTarget('STDOUT'), 0);
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
    public function testLine(
        string $expect,
        string $message,
        ...$args
    ) : void {
        $this->console->line($message, ...$args);

        $this->assertConsoleTargetContains('STDOUT', $expect);
    }

    /**
     * @param mixed $expect
     * @param mixed $object
     *
     * @return void
     *
     * @dataProvider objectProvider
     */
    public function testDump($expect, $object) : void
    {
        $this->console->dump($object);

        $this->assertConsoleTargetContains('STDOUT', $expect);
    }

    /**
     * @return array
     */
    public function objectProvider() : array
    {
        return [
            [1, 1],
            ['foo', 'foo'],
            ['[foo] => bar', ['foo' => 'bar']],
            ['[foo] =>   [bar] => baz'.PHP_EOL, ['foo' => ['bar' => 'baz']]],
            ['[signature]        => <uint64> 01 01 01 01 01 01 21 01', new Bucket()],
            ['(request 1001) Handshake', new Handshake()],
            ['<uint64> 00 00 00 00 00 00 00 00 (0)', new Uint64()],
            ['<bytestring, 3 bytes> 666f6f (foo)', new Bytestring('foo')],
            ['[0] => <bytestring, 3 bytes> 666f6f (foo)', new Bytearray([new Bytestring('foo')])],
            ['[foo] => <uint64> 00 00 00 00 00 00 00 27 (39)', new Section(['foo' => new Uint64(39)])],
        ];
    }

    /**
     * @return void
     */
    public function testStartBlock() : void
    {
        $this->console->startBlock()->indent()->line('foo');
        $this->assertConsoleTargetEquals('STDOUT', '  foo');
    }

    /**
     * @return void
     */
    public function testEndBlock() : void
    {
        $this->console->startBlock()->endBlock()->indent()->line('foo');
        $this->assertConsoleTargetEquals('STDOUT', 'foo');
    }

    /**
     * @return void
     */
    public function testEol() : void
    {
        $this->console->eol();
        $this->assertConsoleTargetEquals('STDOUT', PHP_EOL);
    }

    /**
     * @return array
     */
    public function messageProvider() : array
    {
        return [
            ['foo', 'foo'],
            ['foo bar', 'foo %s', 'bar'],
            ['bar foo 123', '%s foo %d', 'bar', 123],
        ];
    }

    /**
     * @param string $target
     *
     * @return resource
     */
    protected function getMockForConsoleTarget(string $target)
    {
        static $streams = [];

        $target = strtoupper($target);

        if (!isset($streams[$target])) {
            $streams[$target] = fopen($this->fs->path("console.$target"), 'w+');
        }

        return $streams[$target];
    }

    /**
     * @param string $target
     *
     * @return string
     */
    protected function getConsoleTargetContents(string $target) : string
    {
        $buffer = '';
        $mock = $this->getMockForConsoleTarget(strtoupper($target));

        rewind($mock);

        while (($line = fgets($mock, 4096)) !== false) {
            $buffer .= $line;
        }

        return $buffer;
    }

    /**
     * @param string $target
     * @param mixed  $expect
     *
     * @return void
     */
    protected function assertConsoleTargetEquals(string $target, $expect) : void
    {
        $this->assertEquals($expect, $this->getConsoleTargetContents($target));
    }

    /**
     * @param string $target
     * @param mixed  $expect
     *
     * @return void
     */
    protected function assertConsoleTargetContains(string $target, $expect) : void
    {
        $contents = $this->getConsoleTargetContents($target);

        $this->assertFalse(
            strpos($contents, (string) $expect) === false,
            "Failed asserting that [$contents] contains [$expect]"
        );
    }
}
