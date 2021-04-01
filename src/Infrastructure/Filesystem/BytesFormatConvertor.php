<?php

declare(strict_types=1);

namespace App\Infrastructure\Filesystem;

final class BytesFormatConvertor implements BytesFormatConvertorInterface
{
    private const units = [
        'B' => 1,
        'K' => 1024,
        'M' => 1024 * 1024,
        'G' => 1024 * 1024 * 1024,
        'T' => 1024 * 1024 * 1024 * 1024,
    ];

    public function parse(string $formattedSize): int
    {
        $formattedSize = trim($formattedSize);
        if ($formattedSize === '-') {
            return 0;
        }
        if ($formattedSize === '') {
            return 0;
        }
        if (!preg_match('!^(\d+)(\.\d+){0,1}([BKMGT])$!', $formattedSize, $matches)) {
            throw new UnitFormatException($formattedSize);
        }
        $value = (int) $matches[1];
        $precision = $matches[2];
        $unitSize = self::units[$matches[3]];
        if (empty($precision)) {
            return $value * $unitSize;
        }

        return $value * $unitSize + ((int) (floatval($precision) * $unitSize));
    }

    public function format($bytes, $precision = 3): string
    {
        $base = log($bytes, 1024);

        return round(1024 ** ($base - floor($base)), $precision) . array_keys(self::units)[floor($base)];
    }
}
