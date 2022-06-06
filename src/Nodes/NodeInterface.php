<?php

declare(strict_types=1);

namespace Denpa\Levin\Nodes;

use Denpa\Levin\Bucket;
use Denpa\Levin\Connection;

interface NodeInterface
{
    /**
     * Registers request handler method.
     *
     * @param string $handler
     * @param string $commands,...
     *
     * @return self
     */
    public function registerRequestHandler(string $handler, string ...$commands);

    /**
     * Registers response handler method.
     *
     * @param string $handler
     * @param string $commands,...
     *
     * @return self
     */
    public function registerResponseHandler(string $handler, string ...$commands);

    /**
     * Handles incoming buckets.
     *
     * @param \Denpa\Levin\Bucket     $bucket
     * @param \Denpa\Levin\Connection $connection
     *
     * @return mixed|void
     */
    public function handle(Bucket $bucket, Connection $connection);

    /**
     * @param string $address
     * @param mixed  $port
     * @param array  $options
     *
     * @return void
     */
    public function connect(string $address, $port, array $options = []): void;
}
