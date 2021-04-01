<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use App\Core\ServiceCloner\Configuration\ConfigurationServiceInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ConfigurationExpressionGenerator implements ConfigurationExpressionGeneratorInterface
{
    private ExpressionLanguage $expressionLanguage;
    private ConfigurationServiceInterface $configurationService;

    public function __construct(
        ConfigurationServiceInterface $configurationService
    ) {
        $this->expressionLanguage = new ExpressionLanguage();
        $this->configurationService = $configurationService;
    }

    public function generate(ContainerParameterDTO $containerParameter, string $configurationExpression): string
    {
        if (mb_substr($configurationExpression, 0, 1) !== '=') {
            return $configurationExpression;
        }

        return (string) $this->expressionLanguage->evaluate(mb_substr($configurationExpression, 1), [
            'containerParameter' => $containerParameter,
            'configurationRoot' => $this->configurationService->getConfiguration()->getConfigurationRoot(),
        ]);
    }
}
