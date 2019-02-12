<?php

namespace Denpa\Levin\Section;

use Denpa\Levin\Types\TypeInterface;

interface SectionInterface
{
    /**
     * @var int
     */
    const PORTABLE_STORAGE_SIGNATUREA = 0x01011101;

    /**
     * @var int
     */
    const PORTABLE_STORAGE_SIGNATUREB = 0x01020101;

    /**
     * @var int
     */
    const PORTABLE_STORAGE_FORMAT_VER = 1;

    /**
     * @var int
     */
    const PORTABLE_RAW_SIZE_MARK_MASK = 0x03;

    /**
     * @var int
     */
    const PORTABLE_RAW_SIZE_MARK_BYTE = 0x00;

    /**
     * @var int
     */
    const PORTABLE_RAW_SIZE_MARK_WORD = 0x01;

    /**
     * @var int
     */
    const PORTABLE_RAW_SIZE_MARK_DWORD = 0x02;

    /**
     * @var int
     */
    const PORTABLE_RAW_SIZE_MARK_INT64 = 0x03;

    /**
     * @param string $key
     * @param \Denpa\Levin\Types\TypeInterface
     *
     * @return self
     */
    public function add(string $key, TypeInterface $value);
}
