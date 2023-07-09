<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration\Object;

final class PostStartWaiter
{
    private string $type;
    private string $expression;
    private int $timeout;

    public function getType(): string
    {
        return $this->type;
    }

    public function getExpression(): string
    {
        return $this->expression;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /** @internal */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /** @internal */
    public function setExpression(string $expression): void
    {
        $this->expression = $expression;
    }

    /** @internal */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }
}
