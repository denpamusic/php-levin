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
        Requests\RequestPeerId::class,
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
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return void
     */
    public function __construct(Bucket $bucket)
    {
        $this->registerCommands();
        $this->bucket = $bucket;
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
     * @return \Denpa\Levin\Bucket
     */
    public function handshake(array $args = []) : Bucket
    {
        return $this->bucket->fill(new Requests\Handshake($args));
    }

    /**
     * @return \Denpa\Levin\Bucket
     */
    public function timedsync(array $args = []) : Bucket
    {
        return $this->bucket->fill(new Requests\TimedSync($args));
    }

    /**
     * @return \Denpa\Levin\Bucket
     */
    public function ping(array $args = []) : Bucket
    {
        return $this->bucket->fill(new Requests\Ping($args));
    }

    /**
     * @return \Denpa\Levin\Bucket
     */
    public function statinfo(array $args = []) : Bucket
    {
        return $this->bucket->fill(new Requests\StatInfo($args));
    }

    /**
     * @return \Denpa\Levin\Bucket
     */
    public function networkstate(array $args = []) : Bucket
    {
        return $this->bucket->fill(new Requests\NetworkState($args));
    }

    /**
     * @return \Denpa\Levin\Bucket
     */
    public function requestpeerid(array $args = []) : Bucket
    {
        return $this->bucket->fill(new Requests\RequestPeerId($args));
    }

    /**
     * @return \Denpa\Levin\Bucket
     */
    public function supportflags(array $args = []) : Bucket
    {
        return $this->bucket->fill(new Requests\SupportFlags($args));
    }

    /**
     * @return void
     */
    protected function registerCommands()
    {
        foreach ($this->handlers as $handler) {
            $this->commands[(new $handler())->getCommandCode()] = $handler;
        }
    }
}
