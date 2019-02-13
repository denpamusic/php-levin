<?php

namespace Denpa\Levin\Exceptions;

use Exception;
use Denpa\Levin\Types\TypeInterface;

class SignatureMismatch extends Exception
{
    /**
     * @var \Denpa\Levin\Types\TypeInterface
     */
    protected $signature;

    /**
     * @param \Denpa\Levin\Types\TypeInterface $signature
     * @param mixed                            $args,...
     *
     * @return void
     */
    public function __construct(TypeInterface $signature, ...$args)
    {
        parent::__construct(...$args);
        $this->signature = $signature;
    }

    /**
     * @return \Denpa\Levin\Type\TypeInterface
     */
    public function getSignature() : TypeInterface
    {
        return $this->signature;
    }
}