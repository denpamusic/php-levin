<?php

namespace Denpa\Levin;

use UnexpectedValueException;

class CommandFactory
{
    /**
     * @var array
     */
    protected $handlers = [
        Requests\Handshake::class,
        Requests\TimedSync::class,
        Requests\Ping::class,
        Requests\StatInfo::class,
        Requests\NetworkState::class,
        Requests\PeerId::class,
        Requests\SupportFlags::class,

        Notifications\NewBlock::class,
        Notifications\NewTransactions::class,
        Notifications\RequestGetObjects::class,
        Notifications\ResponseGetObjects::class,
        Notifications\RequestChain::class,
        Notifications\ResponseChainEntry::class,
        Notifications\NewFluffyBlock::class,
        Notifications\RequestFluffyMissingTx::class,
    ];

    /**
     * @var array
     */
    protected $commands = [];

    /**
     * @return void
     */
    public function __construct()
    {
        $this->registerCommands();
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function getCommand(int $command) : CommandInterface
    {
        if (!isset($this->commands[$command])) {
            throw new UnexpectedValueException("Unknown command [$command]");
        }

        return new $this->commands[$command]();
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function handshake(array $args = []) : CommandInterface
    {
        return new Requests\Handshake($args);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function timedsync(array $args = []) : CommandInterface
    {
        return new Requests\TimedSync($args);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function ping(array $args = []) : CommandInterface
    {
        return new Requests\Ping($args);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function statinfo(array $args = []) : CommandInterface
    {
        return new Requests\StatInfo($args);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function networkstate(array $args = []) : CommandInterface
    {
        return new Requests\NetworkState($args);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function peerid(array $args = []) : CommandInterface
    {
        return new Requests\PeerId($args);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function supportflags(array $args = []) : CommandInterface
    {
        return new Requests\SupportFlags($args);
    }

    /**
     * @return void
     */
    protected function registerCommands() : void
    {
        foreach ($this->handlers as $handler) {
            $this->commands[(new $handler())->getCommandCode()] = $handler;
        }
    }
}
