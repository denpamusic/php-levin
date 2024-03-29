<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\BoostSerializable;
use Denpa\Levin\Types\Uint8;

class Uint8Test extends TestCase
{
    /**
     * @return void
     */
    public function testGetSerializeType(): void
    {
        $this->assertEquals(BoostSerializable::SERIALIZE_TYPE_UINT8, (new Uint8())->getSerializeType()->toInt());
    }

    /**
     * @return void
     */
    public function testToBinary(): void
    {
        $this->assertEquals("\x01", (new Uint8(1))->toBinary());
    }
}
