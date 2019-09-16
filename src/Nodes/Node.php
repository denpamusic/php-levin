<?php

declare(strict_types=1);

namespace Denpa\Levin\Nodes;

use Denpa\Levin;
use Denpa\Levin\Bucket;
use Denpa\Levin\Connection;
use Throwable;

abstract class Node implements NodeInterface
{
    /**
     * @var array Contains handler methods.
     */
    protected $handlers = [];

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
        $this->handlers['request.'.$handler] = $commands;

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
        $this->handlers['response.'.$handler] = $commands;

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
        foreach ($this->handlers as $key => $commands) {
            list($type, $handler) = explode('.', $key, 2);

            $isCommand = [$bucket, 'is'.ucfirst($type)];

            if (
                $isCommand(...$commands) &&
                !$this->$handler($bucket, $connection) === false
            ) {
                return false;
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
            ),
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
