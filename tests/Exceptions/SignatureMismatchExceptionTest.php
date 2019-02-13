<?php

namespace Denpa\Levin\Tests;

use Denpa\Levin\Types\Uint16;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Exceptions\SignatureMismatchException;

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
