<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Types;

use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\Boolean;
use Denpa\Levin\Types\BoostSerializable;

class BooleanTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetSerializeType(): void
    {
        $this->assertEquals(BoostSerializable::SERIALIZE_TYPE_BOOL, (new Boolean())->getSerializeType()->toInt());
    }

    /**
     * @return void
     */
    public function testToBinary(): void
    {
        $this->assertEquals("\x00", (new Boolean(false))->toBinary());
        $this->assertEquals("\x01", (new Boolean(true))->toBinary());
    }
}
