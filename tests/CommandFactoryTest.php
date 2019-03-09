<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests;

use Denpa\Levin\Bucket;
use Denpa\Levin\CommandFactory;
use Denpa\Levin\Exceptions\UnknownCommandException;
use Denpa\Levin\Notifications\NewBlock;
use Denpa\Levin\Notifications\NewFluffyBlock;
use Denpa\Levin\Notifications\NewTransactions;
use Denpa\Levin\Notifications\RequestChain;
use Denpa\Levin\Notifications\RequestFluffyMissingTx;
use Denpa\Levin\Notifications\RequestGetObjects;
use Denpa\Levin\Notifications\ResponseChainEntry;
use Denpa\Levin\Notifications\ResponseGetObjects;
use Denpa\Levin\Requests\Handshake;
use Denpa\Levin\Requests\NetworkState;
use Denpa\Levin\Requests\PeerId;
use Denpa\Levin\Requests\Ping;
use Denpa\Levin\Requests\RequestInterface;
use Denpa\Levin\Requests\StatInfo;
use Denpa\Levin\Requests\SupportFlags;
use Denpa\Levin\Requests\TimedSync;

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
        $this->expectException(UnknownCommandException::class);
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
        $this->assertInstanceOf($handler, $this->commandFactory->$helper());
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
            ['handshake',              Handshake::class],
            ['timedsync',              TimedSync::class],
            ['ping',                   Ping::class],
            ['statinfo',               StatInfo::class],
            ['networkstate',           NetworkState::class],
            ['peerid',                 PeerId::class],
            ['supportflags',           SupportFlags::class],
            ['newblock',               NewBlock::class],
            ['newtransactions',        NewTransactions::class],
            ['requestgetobjects',      RequestGetObjects::class],
            ['responsegetobjects',     ResponseGetObjects::class],
            ['requestchain',           RequestChain::class],
            ['responsechainentry',     ResponseChainEntry::class],
            ['newfluffyblock',         NewFluffyBlock::class],
            ['requestfluffymissingtx', RequestFluffyMissingTx::class],
        ];
    }
}
