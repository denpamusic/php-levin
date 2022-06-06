<?php

declare(strict_types=1);

namespace Denpa\Levin\Types;

class Int16 extends SignedInt implements BoostSerializable
{
    /**
     * @return string
     */
    protected function getTypeCode(): string
    {
        return 's';
    }

    /**
     * @return \Denpa\Levin\Types\Uint8
     */
    public function getSerializeType(): Uint8
    {
        return new Uint8(self::SERIALIZE_TYPE_INT16);
    }
}
