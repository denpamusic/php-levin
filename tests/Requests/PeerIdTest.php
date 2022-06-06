<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Requests;

use Denpa\Levin;
use Denpa\Levin\Requests\PeerId;

class PeerIdTest extends RequestTest
{
    /**
     * @var string
     */
    protected $classname = PeerId::class;

    /**
     * @return void
     */
    public function testRequest(): void
    {
        $this->assertRequestMap();
    }

    /**
     * @return void
     */
    public function testResponse(): void
    {
        $this->assertResponseMap([
            'my_id' => Levin\uint64le(),
        ]);
    }

    /**
     * @return void
     */
    public function testGetCommandCode(): void
    {
        $this->assertCommandCode(6);
    }

    /**
     * @return void
     */
    public function testVars(): void
    {
        $this->assertVars([
            'my_id' => Levin\peer_id(),
        ]);
    }
}
