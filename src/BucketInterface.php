<?php

namespace Denpa\Levin;

use Denpa\Levin\Section\Section;

interface BucketInterface
{
    /**
     * @var int
     */
    const LEVIN_SIGNATURE = 0x0101010101012101;

    /**
     * @var int
     */
    const LEVIN_PROTOCOL_VER_1 = 1;

    /**
     * @var int
     */
    const LEVIN_PACKET_REQUEST = 1;

    /**
     * @var int
     */
    const LEVIN_PACKET_RESPONSE = 2;

    /**
     * @var int
     */
    const LEVIN_DEFAULT_MAX_PACKET_SIZE = 100000000; // 100MB

    /**
     * @return string
     */
    public function head() : string;

    /**
     * @return \Denpa\Levin\Section|null
     */
    public function payload() : ?Section;

    /**
     * @param resource $socket
     *
     * @return void
     */
    public function writeTo($socket) : void;

    /**
     * @param resource $socket
     *
     * @return mixed
     */
    public static function readFrom($socket);
}
