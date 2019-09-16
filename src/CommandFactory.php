<?php

declare(strict_types=1);

namespace Denpa\Levin;

use Denpa\Levin\Exceptions\UnknownCommandException;

class CommandFactory
{
    /**
     * @var array
     */
    protected $handlers = [
        // requests
        Requests\Handshake::class,
        Requests\TimedSync::class,
        Requests\Ping::class,
        Requests\StatInfo::class,
        Requests\NetworkState::class,
        Requests\PeerId::class,
        Requests\SupportFlags::class,
        // notifications
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
            throw new UnknownCommandException("Unknown command [$command]");
        }

        return new $this->commands[$command]();
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function handshake(array $vars = []) : CommandInterface
    {
        return new Requests\Handshake($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function timedsync(array $vars = []) : CommandInterface
    {
        return new Requests\TimedSync($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function ping(array $vars = []) : CommandInterface
    {
        return new Requests\Ping($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function statinfo(array $vars = []) : CommandInterface
    {
        return new Requests\StatInfo($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function networkstate(array $vars = []) : CommandInterface
    {
        return new Requests\NetworkState($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function peerid(array $vars = []) : CommandInterface
    {
        return new Requests\PeerId($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function supportflags(array $vars = []) : CommandInterface
    {
        return new Requests\SupportFlags($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function newblock(array $vars = []) : CommandInterface
    {
        return new Notifications\NewBlock($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function newtransactions(array $vars = []) : CommandInterface
    {
        return new Notifications\NewTransactions($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function requestgetobjects(array $vars = []) : CommandInterface
    {
        return new Notifications\RequestGetObjects($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function responsegetobjects(array $vars = []) : CommandInterface
    {
        return new Notifications\ResponseGetObjects($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function requestchain(array $vars = []) : CommandInterface
    {
        return new Notifications\RequestChain($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function responsechainentry(array $vars = []) : CommandInterface
    {
        return new Notifications\ResponseChainEntry($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function newfluffyblock(array $vars = []) : CommandInterface
    {
        return new Notifications\NewFluffyBlock($vars);
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function requestfluffymissingtx(array $vars = []) : CommandInterface
    {
        return new Notifications\RequestFluffyMissingTx($vars);
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
