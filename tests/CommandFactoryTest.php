<?php

namespace Denpa\Levin\Tests;

use Denpa\Levin\Bucket;
use Denpa\Levin\CommandFactory;
use Denpa\Levin\Requests\RequestInterface;
use Denpa\Levin\Requests\Handshake;
use Denpa\Levin\Requests\TimedSync;
use Denpa\Levin\Requests\Ping;
use Denpa\Levin\Requests\StatInfo;
use Denpa\Levin\Requests\NetworkState;
use Denpa\Levin\Requests\RequestPeerId;
use Denpa\Levin\Requests\SupportFlags;
use UnexpectedValueException;

class CommandFactoryTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();
        $this->bucket = $this->createMock(Bucket::class);
        $this->commandFactory = new CommandFactory($this->bucket);
    }

    /**
     * @param int    $command
     * @param string $handler
     *
     * @return void
     *
     * @dataProvider handlerProvider
     */
    public function testGetCommand(int $command, string $handler) : void
    {
        $this->assertInstanceOf($handler, $this->commandFactory->getCommand($command));
    }

    /**
     * @return void
     */
    public function testGetCommandWithUnknown() : void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Unknown command [9999]');
        $this->commandFactory->getCommand(9999);
    }

    /**
     * @return void
     *
     * @dataProvider helperProvider
     */
    public function testCommandHelpers(string $helper, string $handler) : void
    {
        $this->bucket
            ->expects($this->once())
            ->method('fill')
            ->with($this->isInstanceOf($handler))
            ->willReturnSelf();

        $this->assertInstanceOf(Bucket::class, $this->commandFactory->$helper());
    }

    /**
     * @return array
     */
    public function handlerProvider() : array
    {
        return [
            [RequestInterface::P2P_COMMANDS_POOL_BASE + 1, Handshake::class],
            [RequestInterface::P2P_COMMANDS_POOL_BASE + 2, TimedSync::class],
            [RequestInterface::P2P_COMMANDS_POOL_BASE + 3, Ping::class],
        ];
    }

    /**
     * @return array
     */
    public function helperProvider() : array
    {
        return [
            ['handshake',     Handshake::class],
            ['timedsync',     TimedSync::class],
            ['ping',          Ping::class],
            ['statinfo',      StatInfo::class],
            ['networkstate',  NetworkState::class],
            ['requestpeerid', RequestPeerId::class],
            ['supportflags',  SupportFlags::class],
        ];
    }
}
