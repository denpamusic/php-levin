<?php

namespace Denpa\Levin;

use Denpa\Levin\Connection;
use Denpa\Levin\Section\Reader;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Types\Int32;
use Denpa\Levin\Types\Uint32;
use Denpa\Levin\Types\Uint64;
use Denpa\Levin\Types\Boolean;
use Denpa\Levin\Types\TypeInterface;

class Bucket implements BucketInterface
{
    /**
     * @var \Denpa\Levin\Types\Uint64
     */
    protected $signature;

    /**
     * @var \Denpa\Levin\Types\Uint64|null
     */
    protected $cb;

    /**
     * @var \Denpa\Levin\Types\Uint32
     */
    protected $returnData;

    /**
     * @var \Denpa\Levin\CommandInterface
     */
    protected $command;

    /**
     * @var \Denpa\Levin\Types\Int32
     */
    protected $returnCode;

    /**
     * @var \Denpa\Levin\Types\Uint32
     */
    protected $flags;

    /**
     * @var \Denpa\Levin\Types\Uint32
     */
    protected $protocolVersion;

    /**
     * @var \Denpa\Levin\Section|null
     */
    protected $payloadSection = null;

    /**
     * @return void
     */
    public function __construct(array $params = [])
    {
        // set some defaults
        $defaults = [
            'signature'        => self::LEVIN_SIGNATURE,
            'cb'               => 0,
            'return_data'      => true,
            'return_code'      => 0,
            'protocol_version' => self::LEVIN_PROTOCOL_VER_1,
            'flags'            => self::LEVIN_PACKET_REQUEST,
        ];

        $params = $params + $defaults;

        foreach ($params as $key => $value) {
            $mutator = 'set'.camel_case($key);
            if (method_exists($this, $mutator)) {
                $this->$mutator($value);
            }
        }
    }

    /**
     * @param mixed $signature
     *
     * @return self
     */
    public function setSignature($signature) : self
    {
        $this->signature = $signature instanceof Uint64 ?
            $signature : uint64le($signature);

        if ($this->signature != uint64le(self::LEVIN_SIGNATURE)) {
            $signature = $this->signature->toHex();

            throw new \Exception("Packet signature mismatch [$signature]");
        }

        return $this;
    }

    /**
     * @param mixed $cb
     *
     * @return self
     */
    public function setCb($cb) : self
    {
        $this->cb = $cb instanceof Uint64 ? $cb : uint64le($cb);

        if ($this->cb->toInt() > self::LEVIN_DEFAULT_MAX_PACKET_SIZE) {
            $maxsize = self::LEVIN_DEFAULT_MAX_PACKET_SIZE;

            throw new \Exception("Packet is too large [> $maxsize]");
        }

        return $this;
    }

    /**
     * @return \Denpa\Levin\Types\Uint64
     */
    public function getCb() : Uint64
    {
        return $this->cb;
    }

    /**
     * @param mixed $returnData
     *
     * @return self
     */
    public function setReturnData($returnData) : self
    {
        $this->returnData = $returnData instanceof Boolean ?
            $returnData : boolean($returnData);

        return $this;
    }

    /**
     * @param mixed $command
     *
     * @return self
     */
    public function setCommand($command) : self
    {
        $command = $command instanceof Uint32 ?
            $command : uint32le($command);

        $this->command = (new CommandFactory($this))->getCommand($command->toInt());

        return $this;
    }

    /**
     * @return \Denpa\Levin\CommandInterface|null
     */
    public function getCommand() : ?CommandInterface
    {
        return $this->command;
    }

    /**
     * @param \Denpa\Levin\CommandInterface $command
     *
     * @return self
     */
    public function fill(CommandInterface $command) : self
    {
        $method = ($this->flags->toInt() == self::LEVIN_PACKET_REQUEST) ?
            'request' : 'response';

        $this->command = $command;
        $this->setPayloadSection($command->$method());

        return $this;
    }

    /**
     * @param mixed $returnCode
     *
     * @return self
     */
    public function setReturnCode($returnCode = 0) : self
    {
        $this->returnCode = $returnCode instanceof Int32 ?
            $returnCode : int32le($returnCode);

        return $this;
    }

    /**
     * @param mixed $flags
     *
     * @return self
     */
    public function setFlags($flags) : self
    {
        $this->flags = $flags instanceof Uint32 ? $flags : uint32le($flags);

        return $this;
    }

    /**
     * @param mixed $protocolVersion
     *
     * @return self
     */
    public function setProtocolVersion($protocolVersion) : self
    {
        $this->protocolVersion = $protocolVersion instanceof Uint32 ?
            $protocolVersion : uint32le($protocolVersion);

        return $this;
    }

    /**
     * @param \Denpa\Levin\Section $section
     *
     * @return self
     */
    public function setPayloadSection(Section $section) : self
    {
        $this->setCb($section->getByteSize());
        $this->payloadSection = $section;

        return $this;
    }

    /**
     * @return string
     */
    public function head() : string
    {
        $head = [
            'signature'        => $this->signature,
            'cb'               => $this->cb,
            'return_data'      => $this->returnData,
            'command'          => $this->command,
            'return_code'      => $this->returnCode,
            'flags'            => $this->flags,
            'protocol_version' => $this->protocolVersion,
        ];

        array_walk($head, function (&$item, $key) {
            if (is_null($item)) {
                throw new \Exception("Value for [$key] must be set");
            }

            if ($item instanceof CommandInterface) {
                $item = $item->getCommand();
            }

            $item = $item->toBinary();
        });

        return implode('', $head);
    }

    /**
     * @return \Denpa\Levin\Section|null
     */
    public function payload() : ?Section
    {
        return $this->payloadSection;
    }

    /**
     * @return array
     */
    public function serialize() : array
    {
        return [$this->head(), $this->payload()];
    }

    /**
     * @param \Denpa\Levin\Connection $connection
     *
     * @return void
     */
    public function write(Connection $connection) : void
    {
        $connection->write($this->head());

        if (!is_null($this->payloadSection)) {
            $connection->write($this->payload()->toBinary());
        }
    }

    /**
     * @param Connection $connection
     *
     * @return mixed
     */
    public function read(Connection $connection)
    {
        if ($connection->eof()) {
            return;
        }

        $bucket = new static([
            'signature'        => $connection->read(uint64le()),
            'cb'               => $connection->read(uint64le()),
            'return_data'      => $connection->read(boolean()),
            'command'          => $connection->read(uint32le()),
            'return_code'      => $connection->read(int32le()),
            'flags'            => $connection->read(uint32le()),
            'protocol_version' => $connection->read(uint32le()),
        ]);

        if ($bucket->getCb()->toInt() > 0) {
            $section = (new Reader($connection))->read();
            $bucket->setPayloadSection($section);
        }

        return $bucket;
    }

    /**
     * @return \Denpa\Levin\CommandFactory
     */
    public static function request() : CommandFactory
    {
        $bucket = new static([
            'return_data' => true,
            'return_code' => 0,
            'flags'       => self::LEVIN_PACKET_REQUEST,
        ]);

        return new CommandFactory($bucket);
    }

    /**
     * @return \Denpa\Levin\CommandFactory
     */
    public static function response() : CommandFactory
    {
        $bucket = new static([
            'return_data' => false,
            'return_code' => 0,
            'flags'       => self::LEVIN_PACKET_RESPONSE,
        ]);

        return new CommandFactory($bucket);
    }
}
