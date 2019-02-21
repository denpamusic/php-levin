<?php

namespace Denpa\Levin\Tests;

use BadMethodCallException;
use Denpa\Levin;
use Denpa\Levin\Bucket;
use Denpa\Levin\CommandInterface;
use Denpa\Levin\Connection;
use Denpa\Levin\Exceptions\EntryTooLargeException;
use Denpa\Levin\Exceptions\SignatureMismatchException;
use Denpa\Levin\Requests\Handshake;
use Denpa\Levin\Requests\RequestInterface;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Types\Boolean;
use Denpa\Levin\Types\Int32;
use Denpa\Levin\Types\Uint32;
use Denpa\Levin\Types\Uint64;
use Denpa\Levin\Types\Uint8;
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

        $this->bucket = new Bucket();

        $this->headBytemap = [
            'signature'        => count(new Uint64()),
            'cb'               => count(new Uint64()),
            'return_data'      => count(new Boolean()),
            'command'          => count(new Uint32()),
            'return_code'      => count(new Int32()),
            'flags'            => count(new Uint32()),
            'protocol_version' => count(new Uint32()),
        ];
    }

    /**
     * @return void
     */
    public function testIsRequest() : void
    {
        $handshake = new Handshake();
        $request = (new Bucket())->request($handshake);
        $this->assertTrue($request->isRequest('handshake'));
        $this->assertTrue($request->isRequest());
        $this->assertFalse($request->isRequest('ping'));

        $response = (new Bucket())->response($handshake);
        $this->assertFalse($response->isRequest('handshake'));
        $this->assertFalse($response->isRequest());
    }

    /**
     * @return void
     */
    public function testIsResponse() : void
    {
        $handshake = new Handshake();
        $response = (new Bucket())->response($handshake);
        $this->assertTrue($response->isResponse('handshake'));
        $this->assertTrue($response->isResponse());
        $this->assertFalse($response->isResponse('ping'));

        $request = (new Bucket())->request($handshake);
        $this->assertFalse($request->isResponse('handshake'));
        $this->assertFalse($request->isResponse());
    }

    /**
     * @return void
     */
    public function testIs() : void
    {
        $handshake = new Handshake();
        $bucket = (new Bucket())->fill($handshake);
        $this->assertTrue($bucket->is('handshake'));
        $this->assertFalse($bucket->is('ping'));
    }

    /**
     * @return void
     */
    public function testIsWithMultipleArgs() : void
    {
        $handshake = new Handshake();
        $bucket = (new Bucket())->fill($handshake);
        $this->assertTrue($bucket->is('handshake', 'ping'));
        $this->assertTrue($bucket->is('supportflags', 'handshake'));
        $this->assertFalse($bucket->is('ping', 'supportflags'));
    }

    /**
     * @return void
     */
    public function testSetSignature() : void
    {
        $this->bucket->setSignature(Bucket::LEVIN_SIGNATURE);
        $this->assertInstanceOf(Uint64::class, $this->bucket->getSignature());
        $this->assertSame(Bucket::LEVIN_SIGNATURE, $this->bucket->getSignature()->toInt());
    }

    /**
     * @return void
     */
    public function testSetSignatureWithUint() : void
    {
        $uint64 = new Uint64(Bucket::LEVIN_SIGNATURE, Uint64::LE);
        $this->bucket->setSignature($uint64);
        $this->assertInstanceOf(Uint64::class, $this->bucket->getSignature());
        $this->assertSame(Bucket::LEVIN_SIGNATURE, $this->bucket->getSignature()->toInt());
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
        $this->assertInstanceOf(Uint64::class, $this->bucket->getCb());
        $this->assertSame(100, $this->bucket->getCb()->toInt());
    }

    /**
     * @return void
     */
    public function testSetCbWithUint() : void
    {
        $uint64 = new Uint64(100, Uint64::LE);
        $this->bucket->setCb($uint64);
        $this->assertInstanceOf(Uint64::class, $this->bucket->getCb());
        $this->assertSame(100, $this->bucket->getCb()->toInt());
    }

    /**
     * @return void
     */
    public function testSetCbMaxSize() : void
    {
        $maxsize = Bucket::LEVIN_DEFAULT_MAX_PACKET_SIZE;
        $this->expectException(EntryTooLargeException::class);
        $this->expectExceptionMessage("Bucket is too large [> $maxsize]");
        $this->bucket->setCb($maxsize + 1);
    }

    /**
     * @return void
     */
    public function testSetReturnData() : void
    {
        $this->bucket->setReturnData(false);
        $this->assertInstanceOf(Boolean::class, $this->bucket->getReturnData());
        $this->assertSame(false, $this->bucket->getReturnData()->getValue());
    }

    /**
     * @return void
     */
    public function testSetReturnDataWithUint() : void
    {
        $boolean = new Boolean(false);
        $this->bucket->setReturnData($boolean);
        $this->assertInstanceOf(Boolean::class, $this->bucket->getReturnData());
        $this->assertSame(false, $this->bucket->getReturnData()->getValue());
    }

    /**
     * @return void
     */
    public function testSetCommand() : void
    {
        $this->bucket->setCommand(RequestInterface::P2P_COMMANDS_POOL_BASE + 1);
        $this->assertInstanceOf(RequestInterface::class, $this->bucket->getCommand());
        $this->assertSame(RequestInterface::P2P_COMMANDS_POOL_BASE + 1, $this->bucket->getCommand()->getCommandCode());
    }

    /**
     * @return void
     */
    public function testSetCommandWithUint()
    {
        $uint32 = new Uint32(RequestInterface::P2P_COMMANDS_POOL_BASE + 1, Uint32::LE);
        $this->bucket->setCommand($uint32);
        $this->assertInstanceOf(RequestInterface::class, $this->bucket->getCommand());
        $this->assertSame(RequestInterface::P2P_COMMANDS_POOL_BASE + 1, $this->bucket->getCommand()->getCommandCode());
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
        $this->assertSame($handshake->getCommandCode(), $this->bucket->getCommand()->getCommandCode());
        $this->assertSame($handshake->request()['network_id'], $this->bucket->getCommand()->request()['network_id']);
    }

    /**
     * @return void
     */
    public function testSetReturnCode() : void
    {
        $this->bucket->setReturnCode(0);
        $this->assertInstanceOf(Int32::class, $this->bucket->getReturnCode());
        $this->assertSame(0, $this->bucket->getReturnCode()->toInt());
    }

    /**
     * @return void
     */
    public function testSetReturnCodeWithInt() : void
    {
        $int32 = new Int32(0, Int32::LE);
        $this->bucket->setReturnCode($int32);
        $this->assertInstanceOf(Int32::class, $this->bucket->getReturnCode());
        $this->assertSame(0, $this->bucket->getReturnCode()->toInt());
    }

    /**
     * @return void
     */
    public function testSetFlags() : void
    {
        $this->bucket->setFlags(Bucket::LEVIN_PACKET_REQUEST);
        $this->assertInstanceOf(Uint32::class, $this->bucket->getFlags());
        $this->assertSame(Bucket::LEVIN_PACKET_REQUEST, $this->bucket->getFlags()->toInt());
    }

    /**
     * @return void
     */
    public function testSetFlagsWithUint() : void
    {
        $uint32 = new Uint32(Bucket::LEVIN_PACKET_REQUEST, Uint32::LE);
        $this->bucket->setFlags($uint32);
        $this->assertInstanceOf(Uint32::class, $this->bucket->getFlags());
        $this->assertSame(Bucket::LEVIN_PACKET_REQUEST, $this->bucket->getFlags()->toInt());
    }

    /**
     * @return void
     */
    public function testSetProtocolVersion() : void
    {
        $this->bucket->setProtocolVersion(Bucket::LEVIN_PROTOCOL_VER_1);
        $this->assertInstanceOf(Uint32::class, $this->bucket->getProtocolVersion());
        $this->assertSame(Bucket::LEVIN_PROTOCOL_VER_1, $this->bucket->getProtocolVersion()->toInt());
    }

    /**
     * @return void
     */
    public function testSetProtocolVersionWithUint() : void
    {
        $uint32 = new Uint32(Bucket::LEVIN_PROTOCOL_VER_1, Uint32::LE);
        $this->bucket->setProtocolVersion($uint32);
        $this->assertInstanceOf(Uint32::class, $this->bucket->getProtocolVersion());
        $this->assertSame(Bucket::LEVIN_PROTOCOL_VER_1, $this->bucket->getProtocolVersion()->toInt());
    }

    /**
     * @return void
     */
    public function testSetPayload() : void
    {
        $section = new Section(['foo' => new Uint32(0, Uint32::LE)]);
        $this->bucket->setPayload($section);
        $this->assertSame($section['foo'], $this->bucket->getPayload()['foo']);
    }

    /**
     * @return void
     */
    public function testGetHead() : void
    {
        $handshake = new Handshake();
        $this->bucket->fill($handshake);

        $offset = 0;
        $head = $this->bucket->getHead();
        foreach ($this->headBytemap as $key => $size) {
            $getter = 'get'.ucfirst(Levin\camel_case($key));
            $item = ($this->bucket->$getter() instanceof CommandInterface) ?
            $this->bucket->$getter()->getCommand() : $this->bucket->$getter();

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
        $this->bucket->getHead();
    }

    /**
     * @return void
     */
    public function testPayload() : void
    {
        $handshake = new Handshake();
        $this->bucket->fill($handshake);
        $this->assertSame($handshake->request()['network_id'], $this->bucket->getPayload()['network_id']);
    }

    /**
     * @return void
     */
    public function testRequest() : void
    {
        $handshake = new Handshake();
        $request = (new Bucket())->request($handshake);
        $this->assertInstanceOf(Bucket::class, $request);
        $this->assertEquals(Bucket::LEVIN_PACKET_REQUEST, $request->getFlags()->toInt());
        $this->assertEquals($handshake, $request->getCommand());
    }

    /**
     * @return void
     */
    public function testResponse() : void
    {
        $handshake = new Handshake();
        $response = (new Bucket())->response($handshake);
        $this->assertInstanceOf(Bucket::class, $response);
        $this->assertEquals(Bucket::LEVIN_PACKET_RESPONSE, $response->getFlags()->toInt());
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
                [$this->bucket->getHead()],
                [$this->bucket->getPayload()->toBinary()]
            );

        $this->bucket->write($connection);
    }

    /**
     * @return void
     */
    public function testRead() : void
    {
        $section = Levin\section();
        $signatures = $section->getSignatures();
        $connection = $this->createMock(Connection::class);

        $connection->expects($this->once())
            ->method('eof')
            ->willReturn(false);

        $connection->expects($this->exactly(11))
            ->method('read')
            ->withConsecutive(
                /*
                 * BEGIN HEAD
                 */
                [$this->isInstanceOf(Uint64::class)],  // signature
                [$this->isInstanceOf(Uint64::class)],  // cb
                [$this->isInstanceOf(Boolean::class)], // return_data
                [$this->isInstanceOf(Uint32::class)],  // command
                [$this->isInstanceOf(Int32::class)],   // return_code
                [$this->isInstanceOf(Uint32::class)],  // flags
                [$this->isInstanceOf(Uint32::class)],  // protocol_version
                /*
                 * BEGIN SECTION
                 */
                [$this->isInstanceOf(Uint32::class)],  // signature1
                [$this->isInstanceOf(Uint32::class)],  // signature2
                [$this->isInstanceOf(Uint8::class)],   // signature3
                [$this->isInstanceOf(Varint::class)]   // section size
            )
            ->willReturnOnConsecutiveCalls(
                /*
                 * BEGIN HEAD
                 */
                new Uint64(Bucket::LEVIN_SIGNATURE, Uint64::LE),
                new Uint64(strlen($section->toBinary()), Uint64::LE),
                new Boolean(false),
                new Uint32(RequestInterface::P2P_COMMANDS_POOL_BASE + 1, Uint32::LE),
                new Int32(0, Int32::LE),
                new Uint32(Bucket::LEVIN_PACKET_RESPONSE, Uint32::LE),
                new Uint32(Bucket::LEVIN_PROTOCOL_VER_1, Uint32::LE),
                /*
                 * BEGIN SECTION
                 */
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

    /**
     * @return void
     */
    public function testMagicWithUnknownMethod() : void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Method [nonExistent] does not exist');

        $this->bucket->nonExistent();
    }
}
