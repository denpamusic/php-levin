<?php

declare(strict_types=1);

namespace Denpa\Levin\Types;

class Uint16 extends Type implements BoostSerializable
{
    /**
     * @return string
     */
    protected function getTypeCode(): string
    {
        return $this->isBigEndian() ? 'n' : 'v';
    }

    /**
     * @return \Denpa\Levin\Types\Uint8
     */
    public function getSerializeType(): Uint8
    {
        return new Uint8(self::SERIALIZE_TYPE_UINT16);
    }
}
