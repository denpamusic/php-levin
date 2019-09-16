<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin;
use Denpa\Levin\Notifications\NewBlock;

class NewBlockTest extends NotificationTest
{
    /**
     * @var string
     */
    protected $classname = NewBlock::class;

    /**
     * @return void
     */
    public function testRequest() : void
    {
        $this->assertRequestMap([
            'b'                         => Levin\bytestring(),
            'current_blockchain_height' => Levin\uint64le(),
        ]);
    }

    /**
     * @return void
     */
    public function testGetCommandCode() : void
    {
        $this->assertCommandCode(1);
    }

    /**
     * @return void
     */
    public function testVars() : void
    {
        $this->assertVars([
            'block'                     => '',
            'current_blockchain_height' => 0,
        ]);
    }
}
