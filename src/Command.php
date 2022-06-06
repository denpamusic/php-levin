<?php

declare(strict_types=1);

namespace Denpa\Levin;

use Denpa\Levin\Types\Uint32;

abstract class Command implements CommandInterface
{
    /**
     * @var array Contains command variables.
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
     * Allows access to command variables.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->vars[$name] ?? null;
    }

    /**
     * {@inheritdoc}
     *
     * @return \Denpa\Levin\Types\Uint32
     */
    public function getCommand(): Uint32
    {
        return new Uint32($this->getCommandCode(), Uint32::LE);
    }

    /**
     * Gets list of default command variables.
     *
     * @return array
     */
    protected function defaultVars(): array
    {
        return [];
    }
}
