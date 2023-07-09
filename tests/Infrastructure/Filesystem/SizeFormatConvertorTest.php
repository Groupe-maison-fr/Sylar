<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Filesystem;

use App\Infrastructure\Filesystem\BytesFormatConvertorInterface;
use App\Infrastructure\Filesystem\UnitFormatException;
use Tests\AbstractIntegrationTest;

/**
 * @internal
 */
final class SizeFormatConvertorTest extends AbstractIntegrationTest
{
    private BytesFormatConvertorInterface $sizeFormatConvertor;

    /**
     * @test
     */
    public function it_should_parse_empty_data(): void
    {
        self::assertSame(0, $this->sizeFormatConvertor->parse(' '));
        self::assertSame(0, $this->sizeFormatConvertor->parse(''));
        self::assertSame(0, $this->sizeFormatConvertor->parse('-'));
        self::assertSame(0, $this->sizeFormatConvertor->parse('0B'));
    }

    /**
     * @test
     *
     * @dataProvider valid_bytes_data
     */
    public function it_should_parse_valid_string(string $formattedString, int $size): void
    {
        self::assertSame($size, $this->sizeFormatConvertor->parse($formattedString));
    }

    /**
     * @test
     *
     * @dataProvider valid_bytes_data
     */
    public function it_should_format_bytes(string $formattedString, int $size): void
    {
        self::assertSame($formattedString, $this->sizeFormatConvertor->format($size));
    }

    public function valid_bytes_data(): array
    {
        return [
            ['1B', 1],
            ['1023B', 1023],
            ['1K', 1024],
            ['1023K', 1024 * 1023],
            ['1M', 1024 * 1024],
            ['1023M', 1024 * 1024 * 1023],
            ['1G', 1024 * 1024 * 1024],
            ['1023G', 1024 * 1024 * 1024 * 1023],
            ['1T', 1024 * 1024 * 1024 * 1024],
            ['1023T', 1024 * 1024 * 1024 * 1024 * 1023],
            ['1.222K', 1251],
            ['1.21K', 1239],
            ['1.211K', 1240],
            ['1.475M', 1546649],
            ['1.2M', 1258291],
            ['139.7M', 146486067],
            ['1.3G', 1395864371],
            ['724.1G', 777496454758],
            ['1.1T', 1209462790553],
        ];
    }

    /**
     * @test
     *
     * @dataProvider it_should_not_parse_unformatted_string_data
     */
    public function it_should_not_parse_unformatted_string(string $badString): void
    {
        self::expectException(UnitFormatException::class);
        self::expectExceptionMessage(sprintf('Size "%s" could not be parsed', trim($badString)));
        $this->sizeFormatConvertor->parse($badString);
    }

    public function it_should_not_parse_unformatted_string_data(): array
    {
        return [
            [' A12B'],
            ['123MAB'],
            ['B'],
            ['K12'],
            [' 12 B'],
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->sizeFormatConvertor = $this->getService(BytesFormatConvertorInterface::class);
    }
}
