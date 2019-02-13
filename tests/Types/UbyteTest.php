<?php

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\BoostSerializable;
use Denpa\Levin\Types\Ubyte;

class UbyteTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetSerializeType() : void
    {
        $this->assertEquals(BoostSerializable::SERIALIZE_TYPE_UINT8, (new Ubyte())->getSerializeType()->toInt());
    }

    /**
     * @return void
     */
    public function testToBinary() : void
    {
        $this->assertEquals("\x01", (new Ubyte(1))->toBinary());
    }
}
