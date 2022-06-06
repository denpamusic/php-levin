<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin;
use Denpa\Levin\Notifications\ResponseChainEntry;

class ResponseChainEntryTest extends NotificationTest
{
    /**
     * @var string
     */
    protected $classname = ResponseChainEntry::class;

    /**
     * @return void
     */
    public function testRequest(): void
    {
        $this->assertRequestMap([
            'start_height'          => Levin\uint64le(),
            'total_height'          => Levin\uint64le(),
            'cumulative_difficulty' => Levin\uint64le(),
            'm_block_ids'           => Levin\bytestring(),
        ]);
    }

    /**
     * @return void
     */
    public function testGetCommandCode(): void
    {
        $this->assertCommandCode(7);
    }

    /**
     * @return void
     */
    public function testVars(): void
    {
        $this->assertVars([
            'start_height'          => 0,
            'total_height'          => 0,
            'cumulative_difficulty' => 0,
            'm_block_ids'           => '',
        ]);
    }
}
