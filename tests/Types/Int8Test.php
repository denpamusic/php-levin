<?php

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Types\Int8;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\BoostSerializable;

class Int8Test extends TestCase
{
    /**
     * @return void
     */
    public function testGetSerializeType() : void
    {
        $this->assertEquals(BoostSerializable::SERIALIZE_TYPE_INT8, (new Int8())->getSerializeType()->toInt());
    }

    /**
     * @return void
     */
    public function testToBinary() : void
    {
        $this->assertEquals("\x01", (new Int8(1))->toBinary());
    }
}
