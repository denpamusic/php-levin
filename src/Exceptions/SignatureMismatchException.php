<?php

declare(strict_types=1);

namespace Denpa\Levin\Exceptions;

use Denpa\Levin\Types\TypeInterface;
use Exception;

class SignatureMismatchException extends Exception
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
