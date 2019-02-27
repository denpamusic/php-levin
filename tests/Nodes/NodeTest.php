<?php

namespace Denpa\Levin\Tests\Nodes;

use Denpa\Levin\Bucket;
use Denpa\Levin\Connection;
use Denpa\Levin\Nodes\Node;
use Denpa\Levin\Tests\TestCase;

class NodeTest extends TestCase
{
    /**
     * @return void
     */
    public function testRegisterRequestHandler() : void
    {
        $node = $this->getMockForAbstractClass(Node::class);
        $node->registerRequestHandler('requestHandler', 'request');

        $vars = (array) $node;
        $this->assertEquals(
            ['request.requestHandler' => ['request']],
            $vars["\0*\0handlers"]
        );
    }

    /**
     * @return void
     */
    public function testRegisterResponseHandler() : void
    {
        $node = $this->getMockForAbstractClass(Node::class);
        $node->registerResponseHandler('responseHandler', 'response');

        $vars = (array) $node;
        $this->assertEquals(
            ['response.responseHandler' => ['response']],
            $vars["\0*\0handlers"]
        );
    }

    /**
     * @param string $type
     *
     * @return void
     *
     * @dataProvider handlerProvider
     */
    public function testHandle(string $type) : void
    {
        $node = $this->getMockBuilder(Node::class)
            ->disableOriginalConstructor()
            ->setMethods([$type.'Handler'])
            ->getMockForAbstractClass();

        $register = 'register'.ucfirst($type).'Handler';
        $node->$register($type.'Handler', $type);

        $node->expects($this->once())
            ->method($type.'Handler')
            ->with(
                $this->isInstanceOf(Bucket::class),
                $this->isInstanceOf(Connection::class)
            );

        $bucket = $this->createMock(Bucket::class);

        $bucket->expects($this->once())
            ->method('is'.ucfirst($type))
            ->with($type)
            ->willReturn(true);

        $node($bucket, $this->createMock(Connection::class));
    }

    /**
     * @return array
     */
    public function handlerProvider() : array
    {
        return [
            ['request'],
            ['response'],
        ];
    }

    /**
     * @return void
     */
    public function testConnect() : void
    {
        $socket = $this->createSocketMock(null, '127.0.0.1');

        $this->getMockForAbstractClass(Node::class)
            ->connect(...$socket);

        $bucket = (new Connection(...$socket))->read();
        $networkId = $bucket->getPayload()['node_data']['network_id'];

        $this->assertTrue($bucket->isRequest('handshake'));
        $this->assertEquals(
            '1230f171610441611731008216a1a110',
            $networkId->toHex()
        );
    }
}
