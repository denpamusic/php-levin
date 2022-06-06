<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Traits;

use Denpa\Levin;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Traits\Peerlist;

class PeerlistTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->fake = new FakePeerlist();
        $this->fake->peerlist = [
            [
                'ip'        => '127.0.0.1',
                'port'      => 17022,
                'id'        => Levin\peer_id('deadbeefc01acafe'),
                'last_seen' => 1550169533,
            ],
            [
                'ip'        => '127.0.0.2',
                'port'      => 17022,
                'id'        => Levin\peer_id('0000015eebadc0de'),
                'last_seen' => 1550169533,
            ],
        ];
    }

    /**
     * @return void
     */
    public function testLocalPeerlist(): void
    {
        list($localPeerlist, $localPeerlistNew) = $this->fake->fakeLocalPeerlist();

        $hex = '7f0000017e420000deadbeefc01acafebdb5655c000000007f0000027e4200'.
               '000000015eebadc0debdb5655c00000000';
        $this->assertEquals($hex, bin2hex($localPeerlist));
        $this->assertIsArray($localPeerlistNew);
        $this->assertCount(2, $localPeerlistNew);
        $this->assertInstanceOf(Section::class, $localPeerlistNew[0]);
        $this->assertInstanceOf(Section::class, $localPeerlistNew[1]);
    }

    /**
     * @param array $peer
     * @param array $result
     *
     * @return void
     *
     * @dataProvider peerProvider
     */
    public function testPeerDefaults(array $peer, array $result): void
    {
        $this->fake->fakePeerDefaults($peer);

        foreach ($result as $key => $value) {
            if (!is_null($value)) {
                $this->assertEquals($peer[$key], $value);
            }
        }
    }

    /**
     * @return array
     */
    public function peerProvider(): array
    {
        $now = time();

        return [
            [[], ['ip' => inet_pton('127.0.0.1'), 'port' => 0, 'type' => 0]],
            [['ip' => '127.0.0.2'], ['ip' => inet_pton('127.0.0.2')]],
            [['port' => 1000], ['port' => 1000]],
            [['type' => 3], ['type' => 3]],
            [['last_seen' => $now], ['last_seen' => $now]],
        ];
    }
}

class FakePeerlist
{
    use Peerlist;

    public $peerlist = [];

    public function fakeLocalPeerlist()
    {
        return $this->localPeerlist();
    }

    public function fakePeerDefaults(array &$peer): void
    {
        $this->peerDefaults($peer);
    }
}
