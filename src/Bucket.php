<?php

declare(strict_types=1);

namespace Denpa\Levin;

use BadMethodCallException;
use Denpa\Levin\Exceptions\ConnectionTerminatedException;
use Denpa\Levin\Exceptions\EntryTooLargeException;
use Denpa\Levin\Exceptions\SignatureMismatchException;
use Denpa\Levin\Section\Reader;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Types\Boolean;
use Denpa\Levin\Types\Int32;
use Denpa\Levin\Types\Uint32;
use Denpa\Levin\Types\Uint64;
use UnexpectedValueException;

/**
 * @method \Denpa\Levin\Types\Uint64 getSignature()
 * @method \Denpa\Levin\Types\Uint64 getCb()
 * @method \Denpa\Levin\Types\Uint32 getReturnData()
 * @method \Denpa\Levin\Command      getCommand()
 * @method \Denpa\Levin\Types\Int32  getReturnCode()
 * @method \Denpa\Levin\Types\Uint32 getFlags()
 * @method \Denpa\Levin\Types\Uint32 getProtocolVersion()
 * @method \Denpa\Levin\Section      getPayload()
 */
class Bucket implements BucketInterface
{
    /**
     * @var \Denpa\Levin\Types\Uint64 Levin signature aka Bender's nightmare.
     */
    protected $signature;

    /**
     * @var \Denpa\Levin\Types\Uint64 Bucket size in bytes.
     */
    protected $cb;

    /**
     * @var \Denpa\Levin\Types\Uint32 Indicates whether data should be returned.
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
     * @var \Denpa\Levin\Types\Uint32 Used to indicate bucket type.
     */
    protected $flags;

    /**
     * @var \Denpa\Levin\Types\Uint32
     */
    protected $protocolVersion;

    /**
     * @var \Denpa\Levin\Section|null
     */
    protected $payload = null;

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
            $mutator = 'set'.ucfirst(camel_case($key));
            if (method_exists($this, $mutator)) {
                $this->$mutator($value);
            }
        }
    }

    /**
     * @param string $commands,...
     *
     * @return bool
     */
    public function isRequest(...$commands) : bool
    {
        $isRequest = $this->flags->toInt() == self::LEVIN_PACKET_REQUEST;

        return !empty($commands) ?
            $this->is(...$commands) && $isRequest : $isRequest;
    }

    /**
     * @param string $commands,...
     *
     * @return bool
     */
    public function isResponse(...$commands) : bool
    {
        $isResponse = $this->flags->toInt() == self::LEVIN_PACKET_RESPONSE;

        return !empty($commands) ?
            $this->is(...$commands) && $isResponse : $isResponse;
    }

    /**
     * Checks if bucket contains any of the specified commands.
     *
     * @param string $commands,...
     *
     * @return bool
     */
    public function is(...$commands) : bool
    {
        foreach ($commands as $command) {
            $classname = classname(get_class($this->command));

            if (strtolower($classname) == strtolower($command)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $signature
     *
     * @throws \Denpa\Levin\Exceptions\SignatureMismatchException
     *
     * @return self
     */
    public function setSignature($signature) : self
    {
        $this->signature = $signature instanceof Uint64 ?
            $signature : uint64le($signature);

        if ($this->signature != uint64le(self::LEVIN_SIGNATURE)) {
            throw new SignatureMismatchException($this->signature, 'Packet signature mismatch');
        }

        return $this;
    }

    /**
     * @param mixed $cb
     *
     * @throws \Denpa\Levin\Exceptions\EntryTooLargeException
     *
     * @return self
     */
    public function setCb($cb) : self
    {
        $this->cb = $cb instanceof Uint64 ? $cb : uint64le($cb);

        if ($this->cb->toInt() > self::LEVIN_DEFAULT_MAX_PACKET_SIZE) {
            $maxsize = self::LEVIN_DEFAULT_MAX_PACKET_SIZE;

            throw new EntryTooLargeException("Bucket is too large [> $maxsize]");
        }

        return $this;
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

        $this->command = (new CommandFactory())->getCommand($command->toInt());

        return $this;
    }

    /**
     * Fills the bucket with payload data from command class.
     *
     * @param \Denpa\Levin\CommandInterface|null $command
     *
     * @return self
     */
    public function fill(?CommandInterface $command = null) : self
    {
        $command = $command ?? $this->getCommand();

        if (!is_null($command)) {
            $method = $this->isRequest() ? 'request' : 'response';

            $this->command = $command;
            $this->setPayload($command->$method());
        }

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
    public function setPayload(Section $section) : self
    {
        $this->setCb($section->getByteSize());
        $this->payload = $section;

        return $this;
    }

    /**
     * Gets bucket head.
     *
     * @throws \UnexpectedValueException
     *
     * @return string
     */
    public function getHead() : string
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
                throw new UnexpectedValueException("Value for [$key] must be set");
            }

            if ($item instanceof CommandInterface) {
                $item = $item->getCommand();
            }

            $item = $item->toBinary();
        });

        return implode('', $head);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Denpa\Levin\Connection $connection
     *
     * @return void
     */
    public function write(Connection $connection) : void
    {
        $bucket = $this->getHead();

        if (!is_null($this->payload)) {
            $bucket .= $this->getPayload()->toBinary();
        }

        $connection->write($bucket);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Denpa\Levin\Connection $connection
     *
     * @return self|null
     */
    public function read(Connection $connection) : ?self
    {
        try {
            $bucket = new static([
                'signature'        => $connection->read(uint64le()),
                'cb'               => $connection->read(uint64le()),
                'return_data'      => $connection->read(boolean()),
                'command'          => $connection->read(uint32le()),
                'return_code'      => $connection->read(int32le()),
                'flags'            => $connection->read(uint32le()),
                'protocol_version' => $connection->read(uint32le()),
            ]);

            if ($bucket->cb->toInt() > 0) {
                $section = (new Reader($connection))->read();
                $bucket->setPayload($section);
            }

            return $bucket;
        } catch (ConnectionTerminatedException $exception) {
            return null;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param \Denpa\Levin\CommandInterface|null $command
     *
     * @return self
     */
    public function request(?CommandInterface $command = null) : self
    {
        return $this
            ->setReturnData(true)
            ->setReturnCode(1)
            ->setFlags(self::LEVIN_PACKET_REQUEST)
            ->fill($command);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Denpa\Levin\CommandInterface|null $command
     *
     * @return self
     */
    public function response(?CommandInterface $command = null) : self
    {
        return $this
            ->setReturnData(false)
            ->setReturnCode(1)
            ->setFlags(self::LEVIN_PACKET_RESPONSE)
            ->fill($command);
    }

    /**
     * {@inheritdoc}
     *
     * @param \Denpa\Levin\CommandInterface|null $command
     *
     * @return self
     */
    public function notification(?CommandInterface $command = null) : self
    {
        return $this
            ->request($command)
            ->setReturnData(false);
    }

    /**
     * Allows access to the class variables via magic.
     *
     * @param string $method
     * @param array  $args
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call(string $method, array $args = [])
    {
        if (substr($method, 0, 3) == 'get') {
            $variable = camel_case(substr($method, 3));

            return $this->$variable ?? null;
        }

        throw new BadMethodCallException("Method [$method] does not exist");
    }
}
