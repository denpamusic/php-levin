<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Notifications;

use Denpa\Levin\Notifications\ResponseGetObjects;

class ResponseGetObjectsTest extends NotificationTest
{
    /**
     * @var string
     */
    protected $classname = ResponseGetObjects::class;

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
    public function testGetCommandCode(): void
    {
        $this->assertCommandCode(4);
    }

    /**
     * @return void
     */
    public function testVars(): void
    {
        $this->assertVars();
    }
}
