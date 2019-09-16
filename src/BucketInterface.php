<?php

declare(strict_types=1);

namespace Denpa\Levin;

interface BucketInterface
{
    /**
     * @var int Levin signature aka Bender's nightmare.
     */
    const LEVIN_SIGNATURE = 0x0101010101012101;

    /**
     * @var int
     */
    const LEVIN_PROTOCOL_VER_1 = 1;

    /**
     * @var int Indicates that bucket is request.
     */
    const LEVIN_PACKET_REQUEST = 1;

    /**
     * @var int Indicates that bucket is response.
     */
    const LEVIN_PACKET_RESPONSE = 2;

    /**
     * @var int Specifies maximum bucket size.
     */
    const LEVIN_DEFAULT_MAX_PACKET_SIZE = 100000000; // 100MB

    /**
     * Writes bucket to the connection.
     *
     * @param \Denpa\Levin\Connection $connection
     *
     * @return void
     */
    public function write(Connection $connection) : void;

    /**
     * Read bucket from the connection.
     *
     * @param \Denpa\Levin\Connection $connection
     *
     * @return mixed
     */
    public function read(Connection $connection);

    /**
     * Creates request bucket.
     *
     * @param \Denpa\Levin\CommandInterface|null $command
     *
     * @return self
     */
    public function request(?CommandInterface $command = null);

    /**
     * Creates response bucket.
     *
     * @param \Denpa\Levin\CommandInterface|null $command
     *
     * @return self
     */
    public function response(?CommandInterface $command = null);

    /**
     * Creates notification bucket.
     *
     * @param \Denpa\Levin\CommandInterface|null $command
     *
     * @return self
     */
    public function notification(?CommandInterface $command = null);
}
