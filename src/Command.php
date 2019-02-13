<?php

namespace Denpa\Levin;

use Denpa\Levin\Types\Uint32;

abstract class Command implements CommandInterface
{
    /**
     * @var array
     */
    protected $vars;

    /**
     * @param array $vars
     *
     * @return void
     */
    public function __construct(array $vars = [])
    {
        $this->vars = $vars + $this->defaultVars();
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function __get(string $name)
    {
        return $this->vars[$name] ?? null;
    }

    /**
     * @return \Denpa\Levin\Types\Uint32
     */
    public function getCommand() : Uint32
    {
        return new Uint32($this->getCommandCode(), Uint32::LE);
    }

    /**
     * @return array
     */
    protected function defaultVars() : array
    {
        return [];
    }
}
