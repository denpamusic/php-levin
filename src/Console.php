<?php

namespace Denpa\Levin;

use ArrayAccess;
use Denpa\Levin\Notifications\NotificationInterface;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Types\BoostSerializable;
use Denpa\Levin\Types\Bytearray;
use Denpa\Levin\Types\Bytestring;
use Denpa\Levin\Types\TypeInterface;
use ReflectionClass;

class Console
{
    /**
     * @var int
     */
    protected $level = 0;

    /**
     * @var array Contains array of serializable types.
     */
    protected $types = [];

    /**
     * @var array
     */
    protected $dumpers = [
        CommandInterface::class => 'dumpCommand',
        BucketInterface::class  => 'dumpBucket',
        Section::class          => 'dumpSection',
        Bytearray::class        => 'dumpBytearray',
        Bytestring::class       => 'dumpBytestring',
        ArrayAccess::class      => 'dumpArrayable',
        TypeInterface::class    => 'dumpType',
    ];

    /**
     * @return void
     */
    public function __construct()
    {
        $this->loadTypes();
    }

    /**
     * @param string $message
     * @param mixed  $args,...
     *
     * @return void
     */
    public function line(string $message = '', ...$args) : void
    {
        fwrite(STDOUT, $message == '' ? PHP_EOL : sprintf($message, ...$args));
    }

    /**
     * @param string $message
     * @param mixed  $args,...
     *
     * @return void
     */
    public function error(string $message = '', ...$args) : void
    {
        fwrite(STDERR, $message == '' ? PHP_EOL : sprintf($message, ...$args));
    }

    /**
     * @param mixed $object
     *
     * @return void
     */
    public function dump($object) : void
    {
        if (is_array($object)) {
            $this->dumpArrayable($object);

            return;
        }

        foreach ($this->dumpers as $class => $dumper) {
            if ($object instanceof $class) {
                $this->$dumper($object);

                return;
            }
        }

        var_dump($object);
    }

    /**
     * @param \Denpa\Levin\Bucket
     *
     * @return void
     */
    public function dumpBucket(Bucket $bucket) : void
    {
        $this->line(
            '<%s bucket, payload: %d bytes>'.PHP_EOL,
            $bucket->isRequest() ? 'request' : 'response',
            $bucket->getCb()->toInt()
        );

        $this->line('[head]    =>'.PHP_EOL);
        $this->startBlock();
        $this->dump([
            'signature'        => $bucket->getSignature(),
            'cb'               => $bucket->getCb(),
            'return_data'      => $bucket->getReturnData(),
            'command'          => $bucket->getCommand(),
            'return_code'      => $bucket->getReturnCode(),
            'flags'            => $bucket->getFlags(),
            'protocol_version' => $bucket->getProtocolVersion(),
        ]);
        $this->endBlock();
        $this->line();

        $this->line('[payload] => ');
        $this->startBlock();
        $this->dump($bucket->getPayload());
        $this->endBlock();
        $this->line();
    }

    /**
     * @param Denpa\Levin\CommandInterface $command
     *
     * @return void
     */
    public function dumpCommand(CommandInterface $command) : void
    {
        $type = $command instanceof NotificationInterface
            ? 'notification' : 'request';

        $this->line(
            '(%s %d) %s',
            $type,
            $command->getCommandCode(),
            classname(get_class($command))
        );
    }

    /**
     * @param mixed $arrayable
     *
     * @return void
     */
    public function dumpArrayable($arrayable) : void
    {
        $keyLength = $this->normalizeKeyLength($arrayable);

        foreach ($arrayable as $key => $value) {
            $this->indent();
            $this->line('%s => ', str_pad("[$key]", $keyLength));

            if ($value instanceof ArrayAccess || is_array($value)) {
                $this->startBlock();
                $this->dump($value);
                $this->endBlock();
                continue;
            }

            $this->dump($value);
            $this->line();
        }
    }

    /**
     * @param \Denpa\Levin\Types\TypeInterface $type
     *
     * @return void
     */
    public function dumpType(TypeInterface $type) : void
    {
        $name = strtolower(classname(get_class($type)));

        if ($type instanceof Bytestring) {
            $this->dumpBytestring($type);

            return;
        }

        $this->line(
            '<%s> %s (%d)',
            $name,
            $this->splitHex($type->toHex(), !$type->isBigEndian()),
            $type->toInt()
        );
    }

    /**
     * @param \Denpa\Levin\Types\Bytestring $bytestring
     *
     * @return void
     */
    public function dumpBytestring(Bytestring $bytestring) : void
    {
        $plaintext = preg_replace(
            '/[^a-z0-9!"#$%&\'()*+,.\/:;<=>?@\[\] ^_`{|}~-]+/i',
            '.',
            $bytestring->getValue()
        );

        $plaintext = count($bytestring) == 0 ? '' : " ($plaintext)";

        $this->line(
            '<bytestring, %d bytes> %s%s',
            count($bytestring),
            $bytestring->toHex(),
            $plaintext
        );
    }

    /**
     * @param Denpa\Levin\Types\Bytearray $bytearray
     *
     * @return void
     */
    public function dumpBytearray(Bytearray $bytearray) : void
    {
        $type = $bytearray->getType()->toInt();
        $type = $this->types[$type] ?? $type;

        if (count($bytearray) > 0) {
            $this->line();
            $this->indent();
        }

        $this->line(
            '<bytearray, %d entries of type %s>',
            count($bytearray),
            $type
        );
        $this->line();

        $this->dumpArrayable($bytearray);
    }

    /**
     * @param \Denpa\Levin\Section\Section $section
     *
     * @return void
     */
    public function dumpSection(Section $section) : void
    {
        $this->line();
        $this->indent();
        $this->line('<section, %d entries>', count($section));
        $this->line();
        $this->dumpArrayable($section);
    }

    /**
     * @return int
     */
    public function startBlock() : int
    {
        return $this->level++;
    }

    /**
     * @return int
     */
    public function endBlock() : int
    {
        return $this->level--;
    }

    /**
     * @return void
     */
    public function indent() : void
    {
        if ($this->level > 0) {
            $this->line(str_repeat('  ', $this->level));
        }
    }

    /**
     * @return void
     */
    protected function loadTypes() : void
    {
        $serializable = new ReflectionClass(BoostSerializable::class);

        foreach ($serializable->getConstants() as $name => $value) {
            $parts = explode('_', $name);

            if (!in_array('TYPE', $parts)) {
                continue;
            }

            $this->types[$value] = strtolower(end($parts));
        }
    }

    /**
     * @param mixed $arrayable
     *
     * @return int
     */
    protected function normalizeKeyLength($arrayable) : int
    {
        $max = 0;

        foreach ($arrayable as $key => $value) {
            $length = strlen("[$key]");
            $max = $length > $max ? $length : $max;
        }

        return $max;
    }

    /**
     * @param string $hex
     * @param int    $endianness
     * @param int    $size
     *
     * @return void
     */
    protected function splitHex(
        string $hex,
        bool $reverse = false,
        int $size = 2
    ) : string {
        $hex = str_split($hex, $size);

        if ($reverse) {
            $hex = array_reverse($hex);
        }

        return implode(' ', $hex);
    }
}
