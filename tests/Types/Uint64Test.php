<?php

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Types\Uint64;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\BoostSerializable;

class Uint64Test extends TestCase
{
    /**
     * @return void
     */
    public function testGetSerializeType() : void
    {
        $this->assertEquals(BoostSerializable::SERIALIZE_TYPE_UINT64, (new Uint64())->getSerializeType()->toInt());
    }

    /**
     * @return void
     */
    public function testToBinary() : void
    {
        $this->assertEquals("\x00\x00\x00\x00\x00\x00\x00\x01", (new Uint64(1))->toBinary());
    }
}
