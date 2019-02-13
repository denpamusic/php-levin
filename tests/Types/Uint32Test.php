<?php

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\BoostSerializable;
use Denpa\Levin\Types\Uint32;

class Uint32Test extends TestCase
{
    /**
     * @return void
     */
    public function testGetSerializeType() : void
    {
        $this->assertEquals(BoostSerializable::SERIALIZE_TYPE_UINT32, (new Uint32())->getSerializeType()->toInt());
    }

    /**
     * @return void
     */
    public function testToBinary() : void
    {
        $this->assertEquals("\x00\x00\x00\x01", (new Uint32(1))->toBinary());
    }
}
