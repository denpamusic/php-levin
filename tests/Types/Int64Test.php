<?php

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Types\Int64;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\BoostSerializable;

class Int64Test extends TestCase
{
    /**
     * @return void
     */
    public function testGetSerializeType() : void
    {
        $this->assertEquals(BoostSerializable::SERIALIZE_TYPE_INT64, (new Int64())->getSerializeType()->toInt());
    }

    /**
     * @return void
     */
    public function testToBinary() : void
    {
        $this->assertEquals("\x00\x00\x00\x00\x00\x00\x00\x01", (new Int64(1))->toBinary());
    }
}
