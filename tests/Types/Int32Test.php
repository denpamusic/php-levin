<?php

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\BoostSerializable;
use Denpa\Levin\Types\Int32;

class Int32Test extends TestCase
{
    /**
     * @return void
     */
    public function testGetSerializeType() : void
    {
        $this->assertEquals(BoostSerializable::SERIALIZE_TYPE_INT32, (new Int32())->getSerializeType()->toInt());
    }

    /**
     * @return void
     */
    public function testToBinary() : void
    {
        $this->assertEquals("\x00\x00\x00\x01", (new Int32(1))->toBinary());
    }
}
