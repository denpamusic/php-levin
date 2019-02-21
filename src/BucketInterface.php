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
     * @param \Denpa\Levin\Connection $connection
     *
     * @return void
     */
    public function write(Connection $connection) : void;

    /**
     * @param \Denpa\Levin\Connection $connection
     *
     * @return mixed
     */
    public function read(Connection $connection);
}
