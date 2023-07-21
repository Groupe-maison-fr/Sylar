<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final readonly class ConfigurationExpressionGenerator implements ConfigurationExpressionGeneratorInterface
{
    private ExpressionLanguage $expressionLanguage;

    public function __construct(
        private ConfigurationServiceInterface $configurationService,
    ) {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function generate(ContainerParameterDTO $containerParameter, string $configurationExpression): string
    {
        return (string) $this->evaluate($containerParameter, $configurationExpression);
    }

    public function evaluate(ContainerParameterDTO $containerParameter, string $configurationExpression): mixed
    {
        if (mb_substr($configurationExpression, 0, 1) !== '=') {
            return $configurationExpression;
        }

        return $this->expressionLanguage->evaluate(mb_substr($configurationExpression, 1), [
            'containerParameter' => $containerParameter,
            'configurationRoot' => $this->configurationService->getConfiguration()->configurationRoot,
        ]);
    }
}
