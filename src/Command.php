<?php

namespace Denpa\Levin;

use Denpa\Levin\Types\uInt32;

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
     * @param array $args
     *
     * @return void
     */
    public function setArgs(array $args) : void
    {
        $this->args = $args;
    }

    /**
     * @return \Denpa\Levin\Types\uInt32
     */
    public function getCommand() : uInt32
    {
        return new uInt32($this->getCommandCode(), uInt32::LE);
    }

    /**
     * @return array
     */
    protected function defaultVars() : array
    {
        return [];
    }
}
