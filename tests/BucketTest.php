<?php

namespace Denpa\Levin\Tests;

use Denpa\Levin;
use Denpa\Levin\Bucket;
use Denpa\Levin\CommandInterface;
use Denpa\Levin\Connection;
use Denpa\Levin\Exceptions\SignatureMismatchException;
use Denpa\Levin\Requests\Handshake;
use Denpa\Levin\Requests\RequestInterface;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Types\Boolean;
use Denpa\Levin\Types\Int32;
use Denpa\Levin\Types\Uint32;
use Denpa\Levin\Types\Uint64;
use Denpa\Levin\Types\Varint;
use UnexpectedValueException;

class BucketTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->bucket = new FakeBucket();

        $this->headBytemap = [
            'signature'        => count(new Uint64()),
            'cb'               => count(new Uint64()),
            'returnData'       => count(new Boolean()),
            'command'          => count(new Uint32()),
            'returnCode'       => count(new Int32()),
            'flags'            => count(new Uint32()),
            'protocolVersion'  => count(new Uint32()),
        ];
    }

    /**
     * @return void
     */
    public function testIsRequest() : void
    {
        $handshake = new Handshake();
        $this->assertSame(Bucket::LEVIN_PACKET_REQUEST, (new FakeBucket())->request($handshake)->flags->toInt());
        $this->assertSame(Bucket::LEVIN_PACKET_RESPONSE, (new FakeBucket())->response($handshake)->flags->toInt());
    }

    /**
     * @return void
     */
    public function testIs() : void
    {
        $handshake = new Handshake();
        $bucket = (new FakeBucket())->fill($handshake);
        $this->assertTrue($bucket->is('handshake'));
        $this->assertFalse($bucket->is('ping'));
    }

    /**
     * @return void
     */
    public function testSetSignature() : void
    {
        $this->bucket->setSignature(Bucket::LEVIN_SIGNATURE);
        $this->assertInstanceOf(Uint64::class, $this->bucket->signature);
        $this->assertSame(Bucket::LEVIN_SIGNATURE, $this->bucket->signature->toInt());
    }

    /**
     * @return void
     */
    public function testSetSignatureWithUint() : void
    {
        $uint64 = new Uint64(Bucket::LEVIN_SIGNATURE, Uint64::LE);
        $this->bucket->setSignature($uint64);
        $this->assertInstanceOf(Uint64::class, $this->bucket->signature);
        $this->assertSame(Bucket::LEVIN_SIGNATURE, $this->bucket->signature->toInt());
    }

    /**
     * @return void
     */
    public function testSetSignatureWithMismatch() : void
    {
        $this->expectException(SignatureMismatchException::class);
        $this->expectExceptionMessage('Packet signature mismatch');
        $this->bucket->setSignature(0x0);
    }

    /**
     * @return void
     */
    public function testSetCb() : void
    {
        $this->bucket->setCb(100);
        $this->assertInstanceOf(Uint64::class, $this->bucket->cb);
        $this->assertSame(100, $this->bucket->cb->toInt());
    }

    /**
     * @return void
     */
    public function testSetCbWithUint() : void
    {
        $uint64 = new Uint64(100, Uint64::LE);
        $this->bucket->setCb($uint64);
        $this->assertInstanceOf(Uint64::class, $this->bucket->cb);
        $this->assertSame(100, $this->bucket->cb->toInt());
    }

    /**
     * @return void
     */
    public function testSetCbMaxSize() : void
    {
        $maxsize = Bucket::LEVIN_DEFAULT_MAX_PACKET_SIZE;
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Packet is too large [> $maxsize]");
        $this->bucket->setCb($maxsize + 1);
    }

    /**
     * @return void
     */
    public function testSetReturnData() : void
    {
        $this->bucket->setReturnData(false);
        $this->assertInstanceOf(Boolean::class, $this->bucket->returnData);
        $this->assertSame(false, $this->bucket->returnData->getValue());
    }

    /**
     * @return void
     */
    public function testSetReturnDataWithUint() : void
    {
        $boolean = new Boolean(false);
        $this->bucket->setReturnData($boolean);
        $this->assertInstanceOf(Boolean::class, $this->bucket->returnData);
        $this->assertSame(false, $this->bucket->returnData->getValue());
    }

    /**
     * @return void
     */
    public function testSetCommand() : void
    {
        $this->bucket->setCommand(RequestInterface::P2P_COMMANDS_POOL_BASE + 1);
        $this->assertInstanceOf(RequestInterface::class, $this->bucket->command);
        $this->assertSame(RequestInterface::P2P_COMMANDS_POOL_BASE + 1, $this->bucket->command->getCommandCode());
    }

    /**
     * @return void
     */
    public function testSetCommandWithUint()
    {
        $uint32 = new Uint32(RequestInterface::P2P_COMMANDS_POOL_BASE + 1, Uint32::LE);
        $this->bucket->setCommand($uint32);
        $this->assertInstanceOf(RequestInterface::class, $this->bucket->command);
        $this->assertSame(RequestInterface::P2P_COMMANDS_POOL_BASE + 1, $this->bucket->command->getCommandCode());
    }

    /**
     * @return void
     */
    public function testGetCommand() : void
    {
        $this->assertNull($this->bucket->getCommand());
        $this->bucket->setCommand(RequestInterface::P2P_COMMANDS_POOL_BASE + 1);
        $this->assertInstanceOf(RequestInterface::class, $this->bucket->getCommand());
    }

    /**
     * @return void
     */
    public function testFill() : void
    {
        $handshake = new Handshake();
        $this->bucket->fill($handshake);
        $this->assertSame($handshake->getCommandCode(), $this->bucket->command->getCommandCode());
        $this->assertSame($handshake->request()['network_id'], $this->bucket->command->request()['network_id']);
    }

    /**
     * @return void
     */
    public function testSetReturnCode() : void
    {
        $this->bucket->setReturnCode(0);
        $this->assertInstanceOf(Int32::class, $this->bucket->returnCode);
        $this->assertSame(0, $this->bucket->returnCode->toInt());
    }

    /**
     * @return void
     */
    public function testSetReturnCodeWithInt() : void
    {
        $int32 = new Int32(0, Int32::LE);
        $this->bucket->setReturnCode($int32);
        $this->assertInstanceOf(Int32::class, $this->bucket->returnCode);
        $this->assertSame(0, $this->bucket->returnCode->toInt());
    }

    /**
     * @return void
     */
    public function testSetFlags() : void
    {
        $this->bucket->setFlags(Bucket::LEVIN_PACKET_REQUEST);
        $this->assertInstanceOf(Uint32::class, $this->bucket->flags);
        $this->assertSame(Bucket::LEVIN_PACKET_REQUEST, $this->bucket->flags->toInt());
    }

    /**
     * @return void
     */
    public function testSetFlagsWithUint() : void
    {
        $uint32 = new Uint32(Bucket::LEVIN_PACKET_REQUEST, Uint32::LE);
        $this->bucket->setFlags($uint32);
        $this->assertInstanceOf(Uint32::class, $this->bucket->flags);
        $this->assertSame(Bucket::LEVIN_PACKET_REQUEST, $this->bucket->flags->toInt());
    }

    /**
     * @return void
     */
    public function testSetProtocolVersion() : void
    {
        $this->bucket->setProtocolVersion(Bucket::LEVIN_PROTOCOL_VER_1);
        $this->assertInstanceOf(Uint32::class, $this->bucket->protocolVersion);
        $this->assertSame(Bucket::LEVIN_PROTOCOL_VER_1, $this->bucket->protocolVersion->toInt());
    }

    /**
     * @return void
     */
    public function testSetProtocolVersionWithUint() : void
    {
        $uint32 = new Uint32(Bucket::LEVIN_PROTOCOL_VER_1, Uint32::LE);
        $this->bucket->setProtocolVersion($uint32);
        $this->assertInstanceOf(Uint32::class, $this->bucket->protocolVersion);
        $this->assertSame(Bucket::LEVIN_PROTOCOL_VER_1, $this->bucket->protocolVersion->toInt());
    }

    /**
     * @return void
     */
    public function testSetPayloadSection() : void
    {
        $section = new Section(['foo' => new Uint32(0, Uint32::LE)]);
        $this->bucket->setPayloadSection($section);
        $this->assertSame($section['foo'], $this->bucket->payload()['foo']);
    }

    /**
     * @return void
     */
    public function testHead() : void
    {
        $handshake = new Handshake();
        $this->bucket->fill($handshake);

        $offset = 0;
        $head = $this->bucket->head();
        foreach ($this->headBytemap as $key => $size) {
            $item = ($this->bucket->$key instanceof CommandInterface) ?
                $this->bucket->$key->getCommand() : $this->bucket->$key;

            $this->assertEquals($item->toBinary(), substr($head, $offset, $size));
            $offset += $size;
        }
    }

    /**
     * @return void
     */
    public function testHeadWithNotAllValuesSet() : void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Value for [command] must be set');
        $this->bucket->head();
    }

    /**
     * @return void
     */
    public function testPayload() : void
    {
        $handshake = new Handshake();
        $this->bucket->fill($handshake);
        $this->assertSame($handshake->request()['network_id'], $this->bucket->payload()['network_id']);
    }

    /**
     * @return void
     */
    public function testRequest() : void
    {
        $handshake = new Handshake();
        $request = (new FakeBucket())->request($handshake);
        $this->assertInstanceOf(FakeBucket::class, $request);
        $this->assertEquals(Bucket::LEVIN_PACKET_REQUEST, $request->flags->toInt());
        $this->assertEquals($handshake, $request->getCommand());
    }

    /**
     * @return void
     */
    public function testResponse() : void
    {
        $handshake = new Handshake();
        $response = (new FakeBucket())->response($handshake);
        $this->assertInstanceOf(FakeBucket::class, $response);
        $this->assertEquals(Bucket::LEVIN_PACKET_RESPONSE, $response->flags->toInt());
        $this->assertEquals($handshake, $response->getCommand());
    }

    /**
     * @return void
     */
    public function testWrite() : void
    {
        $handshake = new Handshake();
        $this->bucket->fill($handshake);

        $connection = $this->createMock(Connection::class);

        $connection->expects($this->exactly(2))
            ->method('write')
            ->withConsecutive(
                [$this->bucket->head()],
                [$this->bucket->payload()->toBinary()]
            );

        $this->bucket->write($connection);
    }

    /**
     * @return void
     */
    public function testRead() : void
    {
        $signatures = Levin\section()->getSignatures();
        $connection = $this->createMock(Connection::class);

        $connection->expects($this->once())
            ->method('eof')
            ->willReturn(false);

        $connection->expects($this->exactly(7))
            ->method('read')
            ->withConsecutive(
                // head
                [$this->isInstanceOf(Uint64::class)],
                [$this->isInstanceOf(Uint64::class)],
                [$this->isInstanceOf(Boolean::class)],
                [$this->isInstanceOf(Uint32::class)],
                [$this->isInstanceOf(Int32::class)],
                [$this->isInstanceOf(Uint32::class)],
                [$this->isInstanceOf(Uint32::class)],
                // section
                [$this->isInstanceOf(Uint32::class)],
                [$this->isInstanceOf(Uint32::class)],
                [$this->isInstanceOf(Ubyte::class)],
                [$this->isInstanceOf(Uint::class)]
            )
            ->willReturnOnConsecutiveCalls(
                // head
                new Uint64(Bucket::LEVIN_SIGNATURE, Uint64::LE),
                new Uint64(0, Uint64::LE),
                new Boolean(false),
                new Uint32(RequestInterface::P2P_COMMANDS_POOL_BASE + 1, Uint32::LE),
                new Int32(0, Int32::LE),
                new Uint32(Bucket::LEVIN_PACKET_RESPONSE, Uint32::LE),
                new Uint32(Bucket::LEVIN_PROTOCOL_VER_1, Uint32::LE),
                // section
                $signatures[0],
                $signatures[1],
                $signatures[2],
                new Varint(0)
            );


        $this->bucket->read($connection);
    }

    /**
     * @return void
     */
    public function testReadEof() : void
    {
        $connection = $this->createMock(Connection::class);

        $connection->expects($this->once())
            ->method('eof')
            ->willReturn(true);

        $bucket = $this->bucket->read($connection);

        $this->isNull($bucket);
    }
}

class FakeBucket extends Bucket
{
    public $signature;
    public $cb;
    public $returnData;
    public $command;
    public $returnCode;
    public $flags;
    public $protocolVersion;
    public $payloadSection = null;
}
