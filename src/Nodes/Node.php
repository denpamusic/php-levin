<?php

namespace Denpa\Levin\Nodes;

use Denpa\Levin;
use Denpa\Levin\Bucket;
use Denpa\Levin\Connection;
use Throwable;

abstract class Node implements NodeInterface
{
    /**
     * @var array Contains request buckets handler methods.
     */
    protected $requestHandlers = [];

    /**
     * @var array Contains response buckets handler methods.
     */
    protected $responseHandlers = [];

    /**
     * {@inheritdoc}
     *
     * @param string $handler
     * @param string $commands,...
     *
     * @return self
     */
    public function registerRequestHandler(
        string $handler,
        string ...$commands
    ) : self {
        $this->requestHandlers[$handler] = $commands;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $handler
     * @param string $commands,...
     *
     * @return self
     */
    public function registerResponseHandler(
        string $handler,
        string ...$commands
    ) : self {
        $this->responseHandlers[$handler] = $commands;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Denpa\Levin\Bucket     $bucket
     * @param \Denpa\Levin\Connection $connection
     *
     * @return mixed
     */
    public function handle(Bucket $bucket, Connection $connection)
    {
        foreach (['request', 'response'] as $type) {
            foreach ($this->{$type.'Handlers'} as $handler => $commands) {
                $isType = [$bucket, 'is'.ucfirst($type)];

                if ($isType(...$commands)) {
                    if ($this->$handler($bucket, $connection) === false) {
                        // close connection if any of the handlers return false
                        return false;
                    }
                }
            }
        }
    }

    /**
     * @param \Denpa\Levin\Bucket     $bucket
     * @param \Denpa\Levin\Connection $connection
     *
     * @return mixed
     */
    public function __invoke(Bucket $bucket, Connection $connection)
    {
        return $this->handle($bucket, $connection);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $address
     * @param mixed  $port
     * @param array  $options
     *
     * @return void
     */
    public function connect(string $address, $port, array $options = []) : void
    {
        $vars = [
            'network_id' => hex2bin(
                $options['network-id'] ?? '1230f171610441611731008216a1a110'
            )
        ];

        $exceptionHandler = [$this, 'handleException'];

        Levin\connection($address, $port, $vars)
            ->connect($this, $exceptionHandler);
    }

    /**
     * @param \Throwable $exception
     *
     * @return void
     */
    abstract public function handleException(Throwable $exception) : void;
}
