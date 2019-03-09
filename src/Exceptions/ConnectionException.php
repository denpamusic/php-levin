<?php

declare(strict_types=1);

namespace Denpa\Levin\Exceptions;

use Exception;

class ConnectionException extends Exception
{
    /**
     * @param resource $socket
     * @param mixed    $args,...
     *
     * @return void
     */
    public function __construct($socket, ...$args)
    {
        $errno = socket_last_error($socket);
        parent::__construct(socket_strerror($errno), $errno, ...$args);

        socket_clear_error($socket);
    }
}
