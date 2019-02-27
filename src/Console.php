<?php

namespace Denpa\Levin;

use Denpa\Levin\Section\Section;
use Denpa\Levin\Types\Bytearray;
use Denpa\Levin\Types\Bytestring;
use Denpa\Levin\Types\TypeInterface;

class Console
{
    /**
     * @param string $message
     * @param mixed  $args,...
     *
     * @return void
     */
    public function line(string $message = '', ...$args) : void
    {
        fwrite(STDOUT, sprintf($message, ...$args));
    }

    /**
     * @param string $message
     * @param mixed  $args,...
     *
     * @return void
     */
    public function error(string $message = '', ...$args) : void
    {
        fwrite(STDERR, sprintf($message, ...$args));
    }

    /**
     * @param mixed $object
     *
     * @return void
     */
    public function dump($object) : void
    {
        if ($object instanceof Bucket) {
            $output = $this->dumpBucket($object);
        }

        if ($object instanceof TypeInterface) {
            $output = $this->dumpType($object);
        }

        $this->line($output);
    }

    /**
     * @param \Denpa\Levin\Bucket
     *
     * @return string
     */
    public function dumpBucket(Bucket $bucket) : string
    {
        $head = [
            $this->dumpType($bucket->getSignature()),
            $this->dumpType($bucket->getCb()),
            $this->dumpType($bucket->getReturnData()),
            $this->dumpCommand($bucket->getCommand()),
            $this->dumpType($bucket->getReturnCode()),
            $this->dumpType($bucket->getFlags()),
            $this->dumpType($bucket->getProtocolVersion()),
        ];

        foreach ($head as &$var) {
            $var = rtrim($var);
        }

        $head[] = rtrim($this->dumpSection($bucket->getPayload()));

        $template = <<<EOD
    HEAD:
        signature        : %s
        cb               : %s
        return_data      : %s
        command          : %s
        return_code      : %s
        flags            : %s
        protocol_version : %s
    PAYLOAD:
%s\n\n
EOD;

        return sprintf($template, ...$head);
    }

    /**
     * @param Denpa\Levin\CommandInterface $command
     *
     * @return string
     */
    public function dumpCommand(CommandInterface $command) : string
    {
        $classname = classname(get_class($command));

        return sprintf("[%d] %s\n", $command->getCommandCode(), $classname);
    }

    /**
     * @param \Denpa\Levin\Section\Section $section
     *
     * @return string
     */
    public function dumpSection(Section $section) : string
    {
        $output = '';

        foreach ($section as $key => $value) {
            if (!$value instanceof Bytearray && !$value instanceof Section) {
                $output .= sprintf("\t%s : %s", $key, $this->dumpType($value));
            }
        }

        return $output;
    }

    /**
     * @param \Denpa\Levin\Types\TypeInterface $type
     *
     * @return string
     */
    public function dumpType(TypeInterface $type) : string
    {
        $name = strtolower(classname(get_class($type)));

        $hex = $type->toHex();

        if ($type instanceof Bytestring) {
            return $this->dumpBytestring($type);
        }

        return sprintf(
            "<%s> %s\n",
            $name,
            $this->splitHex($hex, !$type->isBigEndian())
        );
    }

    /**
     * @param \Denpa\Levin\Types\Bytestring $bytestring
     *
     * @return string
     */
    public function dumpBytestring(Bytestring $bytestring) : string
    {
        return sprintf(
            '<bytestring> [%d bytes] %s',
            count($bytestring),
            $bytestring->toHex()
        );
    }

    /**
     * @param string $hex
     * @param int    $endianness
     * @param int    $size
     *
     * @return string
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
