<?php

namespace Denpa\Levin;

use Denpa\Levin\Section\Reader;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Types\Int32;
use Denpa\Levin\Types\uInt32;
use Denpa\Levin\Types\uInt64;

class Bucket implements BucketInterface
{
    /**
     * @var \Denpa\Levin\Types\uInt64
     */
    protected $signature;

    /**
     * @var \Denpa\Levin\Types\uInt64|null
     */
    protected $cb;

    /**
     * @var \Denpa\Levin\Types\uInt32
     */
    protected $return_data;

    /**
     * @var \Denpa\Levin\CommandInterface
     */
    protected $command;

    /**
     * @var \Denpa\Levin\Types\Int32
     */
    protected $return_code;

    /**
     * @var \Denpa\Levin\Types\uInt32
     */
    protected $flags;

    /**
     * @var \Denpa\Levin\Types\uInt32
     */
    protected $protocol_version;

    /**
     * @var \Denpa\Levin\Section|null
     */
    protected $payload_section = null;

    /**
     * @return void
     */
    public function __construct(array $params = [])
    {
        // set some defaults
        $defaults = [
            'signature'        => uint64le(self::LEVIN_SIGNATURE),
            'protocol_version' => uint32le(self::LEVIN_PROTOCOL_VER_1),
            'cb'               => uint64le(0),
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
        $this->signature = $signature instanceof uInt64 ?
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
        $this->cb = $cb instanceof uInt64 ? $cb : uint64le($cb);

        if ($this->cb->toInt() > self::LEVIN_DEFAULT_MAX_PACKET_SIZE) {
            $maxsize = self::LEVIN_DEFAULT_MAX_PACKET_SIZE;

            throw new \Exception("Packet is too large [> $maxsize]");
        }

        return $this;
    }

    /**
     * @return \Denpa\Levin\Types\uInt64
     */
    public function getCb() : uInt64
    {
        return $this->cb;
    }

    /**
     * @param mixed $return_data
     *
     * @return self
     */
    public function setReturnData($return_data) : self
    {
        $this->return_data = $return_data instanceof Boolean ?
            $return_data : boolean($return_data);

        return $this;
    }

    /**
     * @param mixed $command
     *
     * @return self
     */
    public function setCommand($command) : self
    {
        $command = $command instanceof uInt32 ?
            $command : uint32le($command);

        $this->command = (new CommandFactory($this))->getCommand($command->toInt());

        return $this;
    }

    /**
     * @return \Denpa\Levin\CommandInterface
     */
    public function getCommand() : CommandInterface
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
     * @param mixed $return_code
     *
     * @return self
     */
    public function setReturnCode($return_code = 0) : self
    {
        $this->return_code = $return_code instanceof Int32 ?
            $return_code : int32le($return_code);

        return $this;
    }

    /**
     * @param mixed $flags
     *
     * @return self
     */
    public function setFlags($flags) : self
    {
        $this->flags = $flags instanceof uInt32 ? $flags : uint32le($flags);

        return $this;
    }

    /**
     * @param mixed $protocol_version
     *
     * @return self
     */
    public function setProtocolVersion($protocol_version) : self
    {
        $this->protocol_version = $protocol_version instanceof uInt32 ?
            $protocol_version : uint32le($protocol_version);

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
        $this->payload_section = $section;

        return $this;
    }

    /**
     * @return string
     */
    public function head() : string
    {
        $head = [
            $this->signature,
            $this->cb,
            $this->return_data,
            $this->command->getCommand(),
            $this->return_code,
            $this->flags,
            $this->protocol_version,
        ];

        return implode('', array_map(function ($item) {
            return $item->toBinary();
        }, $head));
    }

    /**
     * @return \Denpa\Levin\Section|null
     */
    public function payload() : ?Section
    {
        return $this->payload_section;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->serialize();
    }

    /**
     * @return array
     */
    public function serialize() : array
    {
        return [$this->head(), $this->payload()];
    }

    /**
     * @param resource $fp
     *
     * @return void
     */
    public function writeTo($fp) : void
    {
        fwrite($fp, $this->head());

        if (!is_null($this->payload_section)) {
            fwrite($fp, $this->payload()->toBinary());
        }
    }

    /**
     * @return \Denpa\Levin\CommandFactory
     */
    public static function request() : CommandFactory
    {
        $bucket = new self([
            'return_data' => true,
            'return_code' => 0,
            'flags'       => uint32le(self::LEVIN_PACKET_REQUEST),
        ]);

        return new CommandFactory($bucket);
    }

    /**
     * @return \Denpa\Levin\CommandFactory
     */
    public static function response() : CommandFactory
    {
        $bucket = new self([
            'return_data' => false,
            'return_code' => 0,
            'flags'       => uint32le(self::LEVIN_PACKET_RESPONSE),
        ]);

        return new CommandFactory($bucket);
    }

    /**
     * @param resource $fp
     *
     * @return mixed
     */
    public static function readFrom($fp)
    {
        if (feof($fp)) {
            return;
        }

        $bucket = new self([
            'signature'        => fread($fp, count(uint64le())),
            'cb'               => fread($fp, count(uint64le())),
            'return_data'      => fread($fp, count(boolean())),
            'command'          => fread($fp, count(uint32le())),
            'return_code'      => fread($fp, count(int32le())),
            'flags'            => fread($fp, count(uint32le())),
            'protocol_version' => fread($fp, count(uint32le())),
        ]);

        $section = (new Reader($fp))->read();

        return $bucket->setPayloadSection($section);
    }
}
