<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\Object\Label;

interface LabelFactoryInterface
{
    /**
     * @return array{string,string}
     */
    public function createFromConfiguration(ContainerParameterDTO $containerParameter, Label $label): array;
}
