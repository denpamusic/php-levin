<?php

namespace Denpa\Levin\Traits;

use Denpa\Levin\Console;

trait InteractsWithConsole
{
    /**
     * @var \Denpa\Levin\Console
     */
    protected $console;

    /**
     * @param string $message
     * @param mixed  $args,...
     *
     * @return \Denpa\Levin\Console
     */
    protected function console(string $message = '', ...$args) : Console
    {
        if (!$this->console instanceof Console) {
            $this->console = new Console();
        }

        if ($message != '') {
            $this->console->line($message, ...$args);
        }

        return $this->console;
    }
}
