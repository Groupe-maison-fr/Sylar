<?php

declare(strict_types=1);

namespace App\Infrastructure\Filesystem;

interface BytesFormatConvertorInterface
{
    public function parse(string $formattedSize): int;

    public function format(int $bytes, int $precision = 3): string;
}
