<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests;

use Denpa\Levin\Exceptions\SignatureMismatchException;
use Denpa\Levin\Types\Uint16;

class SignatureMismatchExceptionTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetSignature()
    {
        $exception = new SignatureMismatchException(new Uint16(0x0101), 'Signature mismatch');
        $this->assertEquals(0x0101, $exception->getSignature()->toInt());
    }
}
