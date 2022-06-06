<?php

declare(strict_types=1);

namespace Denpa\Levin;

use ArrayAccess;
use Denpa\Levin\Notifications\NotificationInterface;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Traits\ConsoleColor;
use Denpa\Levin\Types\BoostSerializable;
use Denpa\Levin\Types\Bytearray;
use Denpa\Levin\Types\Bytestring;
use Denpa\Levin\Types\TypeInterface;
use ReflectionClass;

class Console
{
    use ConsoleColor;

    /**
     * @var int
     */
    public $level = 0;

    /**
     * @var array Contains array of serializable types.
     */
    protected $types = [];

    /**
     * @var resource
     */
    protected $target = STDOUT;

    /**
     * @var array
     */
    protected $dumpers = [
        'is_string'                     => 'line',
        'is_numeric'                    => 'line',
        'is_array'                      => 'dumpArrayable',
        'is_a.'.CommandInterface::class => 'dumpCommand',
        'is_a.'.BucketInterface::class  => 'dumpBucket',
        'is_a.'.Section::class          => 'dumpSection',
        'is_a.'.Bytearray::class        => 'dumpBytearray',
        'is_a.'.Bytestring::class       => 'dumpBytestring',
        'is_a.'.ArrayAccess::class      => 'dumpArrayable',
        'is_a.'.TypeInterface::class    => 'dumpType',
    ];

    /**
     * @return void
     */
    public function __construct()
    {
        $this->loadTypes();
    }

    /**
     * @param mixed $message
     * @param mixed $args,...
     *
     * @return self
     */
    public function line($message = '', ...$args): self
    {
        fwrite(
            $this->target,
            $message === '' ?
                PHP_EOL : $this->colorize(sprintf((string) $message, ...$args))
        );

        return $this;
    }

    /**
     * @param mixed $object
     *
     * @return self
     */
    public function dump($object): self
    {
        foreach ($this->dumpers as $dumper => $method) {
            $args = explode('.', $dumper);
            $testFn = array_shift($args);

            if ($testFn($object, ...$args)) {
                return $this->$method($object);
            }
        }

        return $this;
    }

    /**
     * @return self
     */
    public function startBlock(): self
    {
        $this->level++;

        return $this;
    }

    /**
     * @return self
     */
    public function endBlock(): self
    {
        $this->level--;

        return $this;
    }

    /**
     * @return self
     */
    public function indent(): self
    {
        if ($this->level > 0) {
            $this->line(str_repeat('  ', $this->level));
        }

        return $this;
    }

    /**
     * Alias for inserting end-of-line.
     *
     * @return self
     */
    public function eol(): self
    {
        return $this->line();
    }

    /**
     * @param resource $target
     *
     * @return self
     */
    public function target($target = STDOUT): self
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return self
     */
    protected function dumpBucket(Bucket $bucket): self
    {
        return $this
            ->resetColors()
            ->color('red')
            ->line(
                '<%s bucket, payload: %d bytes>'.PHP_EOL,
                $bucket->isRequest() ? 'request' : 'response',
                $bucket->getCb()->toInt()
            )
            ->resetColors()
            ->dumpBucketHead($bucket)
            ->dumpBucketPayload($bucket);
    }

    /**
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return self
     */
    protected function dumpBucketHead(Bucket $bucket): self
    {
        return $this->line('[head]    =>')
            ->eol()
            ->startBlock()
            ->dump([
                'signature'        => $bucket->getSignature(),
                'cb'               => $bucket->getCb(),
                'return_data'      => $bucket->getReturnData(),
                'command'          => $bucket->getCommand(),
                'return_code'      => $bucket->getReturnCode(),
                'flags'            => $bucket->getFlags(),
                'protocol_version' => $bucket->getProtocolVersion(),
            ])
            ->endBlock()
            ->eol();
    }

    /**
     * @param \Denpa\Levin\Bucket $bucket
     *
     * @return self
     */
    protected function dumpBucketPayload(Bucket $bucket): self
    {
        return $this
            ->line('[payload] => ')
            ->startBlock()
            ->dump($bucket->getPayload())
            ->endBlock()
            ->eol();
    }

    /**
     * @param Denpa\Levin\CommandInterface $command
     *
     * @return self
     */
    protected function dumpCommand(CommandInterface $command): self
    {
        $type = $command instanceof NotificationInterface
            ? 'notification' : 'request';

        return $this
            ->resetColors()
            ->line('(')
            ->color('bright-yellow')
            ->line('%s %d', $type, $command->getCommandCode())
            ->resetColors()
            ->line(') ')
            ->background('white')
            ->color('black')
            ->line(classname(get_class($command)))
            ->resetColors();
    }

    /**
     * @param mixed $arrayable
     *
     * @return self
     */
    protected function dumpArrayable($arrayable): self
    {
        $keyLength = $this->normalizeKeyLength($arrayable);

        foreach ($arrayable as $key => $value) {
            $this
                ->indent()
                ->resetColors()
                ->line('%s => ', str_pad("[$key]", $keyLength));

            if ($value instanceof ArrayAccess || is_array($value)) {
                $this
                    ->startBlock()
                    ->dump($value)
                    ->endBlock();
                continue;
            }

            $this->dump($value)->eol();
        }

        return $this;
    }

    /**
     * @param \Denpa\Levin\Types\TypeInterface $type
     *
     * @return self
     */
    protected function dumpType(TypeInterface $type): self
    {
        $name = strtolower(classname(get_class($type)));

        return $this
            ->resetColors()
            ->color('red')
            ->line('<%s> ', $name)
            ->resetColors()
            ->line($this->splitHex($type).' (')
            ->color('bright-yellow')
            ->line($type->toInt())
            ->resetColors()
            ->line(')');
    }

    /**
     * @param \Denpa\Levin\Types\Bytestring $bytestring
     *
     * @return self
     */
    protected function dumpBytestring(Bytestring $bytestring): self
    {
        $this
            ->resetColors()
            ->color('red')
            ->line('<bytestring, %d bytes> ', count($bytestring))
            ->resetColors();

        if (count($bytestring) > 0) {
            $plaintext = preg_replace(
                '/[^a-z0-9!"#$%&\'()*+,.\/:;<=>?@\[\] ^_`{|}~-]+/i',
                '.',
                $bytestring->getValue()
            );

            $this
                ->line($bytestring->toHex().' (')
                ->color('bright-yellow')
                ->line('%s', $plaintext)
                ->resetColors()
                ->line(')');
        }

        return $this;
    }

    /**
     * @param Denpa\Levin\Types\Bytearray $bytearray
     *
     * @return self
     */
    protected function dumpBytearray(Bytearray $bytearray): self
    {
        $type = $bytearray->getType()->toInt();
        $type = $this->types[$type] ?? $type;

        if (count($bytearray) > 0) {
            $this->eol()->indent();
        }

        return $this
            ->resetColors()
            ->color('red')
            ->line(
                '<bytearray, %d entries of type %s>'.PHP_EOL,
                count($bytearray),
                $type
            )
            ->resetColors()
            ->dumpArrayable($bytearray);
    }

    /**
     * @param \Denpa\Levin\Section\Section $section
     *
     * @return self
     */
    protected function dumpSection(Section $section): self
    {
        return $this
            ->eol()
            ->resetColors()
            ->color('red')
            ->indent()
            ->line('<section, %d entries>'.PHP_EOL, count($section))
            ->resetColors()
            ->dumpArrayable($section);
    }

    /**
     * @return void
     */
    protected function loadTypes(): void
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
    protected function normalizeKeyLength($arrayable): int
    {
        $keys = (is_object($arrayable) && method_exists($arrayable, 'keys')) ?
            $arrayable->keys() : array_keys($arrayable);

        return count($keys) == 0 ? 0 : max(array_map('strlen', $keys)) + 2;
    }

    /**
     * @param \Denpa\Levin\Types\TypeInterface $hex
     * @param int                              $length
     *
     * @return string
     */
    protected function splitHex(TypeInterface $type, int $length = 2): string
    {
        $arr = str_split($type->toHex(), $length);

        return implode(' ', ($type->isBigEndian() ? $arr : array_reverse($arr)));
    }
}
