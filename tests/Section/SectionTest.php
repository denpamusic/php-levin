<?php

declare(strict_types=1);

namespace Denpa\Levin\Tests\Section;

use Denpa\Levin\Exceptions\UnexpectedTypeException;
use Denpa\Levin\Section\Section;
use Denpa\Levin\Tests\TestCase;
use Denpa\Levin\Types\Bytestring;
use Denpa\Levin\Types\Uint32;
use Denpa\Levin\Types\Uint8;
use Denpa\Levin\Types\Varint;

class SectionTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp() : void
    {
        parent::setUp();

        $this->section = new Section();

        $this->signatures = [
            new Uint32(Section::PORTABLE_STORAGE_SIGNATUREA, Uint32::LE),
            new Uint32(Section::PORTABLE_STORAGE_SIGNATUREB, Uint32::LE),
            new Uint8(Section::PORTABLE_STORAGE_FORMAT_VER),
        ];
    }

    /**
     * @return void
     */
    public function testAdd() : void
    {
        $this->section->add('test', new Uint32(1));
        $this->assertEquals(1, $this->section['test']->toInt());
    }

    /**
     * @return void
     */
    public function testGetSerializeType() : void
    {
        $this->assertEquals($this->section->getSerializeType()->toInt(), Section::SERIALIZE_TYPE_OBJECT);
    }

    /**
     * @return void
     */
    public function testGetSignatures() : void
    {
        $signatures = $this->section->getSignatures();

        $this->assertEquals($this->signatures, $signatures);
    }

    /**
     * also covers serialize().
     *
     * @return void
     */
    public function testToBinary() : void
    {
        $this->section->add('test', new Bytestring('foo'));
        $binary = $this->section->toBinary();
        $signatures = implode('', $this->signatures);
        $offset = 0;

        // signatures
        $this->assertEquals($signatures, substr($binary, $offset, strlen($signatures)));
        $offset += strlen($signatures);

        // section size
        $this->assertEquals((new Varint(1))->toBinary(), substr($binary, $offset, 1));
        $offset += 1;

        // title size
        $this->assertEquals((new Uint8(strlen('test')))->toBinary(), substr($binary, $offset, 1));
        $offset += 1;

        // title
        $this->assertEquals('test', substr($binary, $offset, strlen('test')));
        $offset += strlen('test');

        // data type
        $this->assertEquals((new Bytestring())->getSerializeType()->toBinary(), substr($binary, $offset, 1));
        $offset += 1;

        // data size
        $this->assertEquals((new Varint(3))->toBinary(), substr($binary, $offset, 1));
        $offset += 1;

        // data
        $this->assertEquals((new Bytestring('foo'))->toBinary(), substr($binary, $offset, 3));
    }

    /**
     * @return void
     */
    public function testToBinaryWithInvalidData() : void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->expectExceptionMessage('Cannot serialize unserializable item [test]');
        $this->section['test'] = new Varint(3);
        $this->section->toBinary();
    }

    /**
     * @return void
     */
    public function testToHex() : void
    {
        $this->assertEquals(bin2hex($this->section->toBinary()), $this->section->toHex());
    }

    /**
     * @return void
     */
    public function testGetByteSize() : void
    {
        $this->assertEquals(strlen($this->section->toBinary()), $this->section->getByteSize());
    }

    /**
     * @return void
     */
    public function testGetInternalByteSize() : void
    {
        $this->assertEquals(strlen($this->section->serialize()), $this->section->getInternalByteSize());
    }

    /**
     * @return void
     */
    public function testGetEntries() : void
    {
        $uint32 = new Uint32(1);
        $this->section->add('test', $uint32);

        $this->assertEquals(['test' => $uint32], $this->section->getEntries());
    }

    /**
     * @return void
     */
    public function testToString() : void
    {
        $this->assertEquals($this->section->serialize(), (string) $this->section);
    }
}
