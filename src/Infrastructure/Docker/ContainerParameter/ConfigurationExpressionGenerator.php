<?php

declare(strict_types=1);

namespace App\Infrastructure\Docker\ContainerParameter;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ConfigurationExpressionGenerator implements ConfigurationExpressionGeneratorInterface
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    public function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function generate(ContainerParameterDTO $containerParameter, string $configurationExpression): string
    {
        if (mb_substr($configurationExpression, 0, 1) !== '=') {
            return $configurationExpression;
        }

        return (string) $this->expressionLanguage->evaluate(mb_substr($configurationExpression, 1), [
            'containerParameter' => $containerParameter,
        ]);
    }
}
