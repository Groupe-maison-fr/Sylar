<?php

declare(strict_types=1);

namespace App\UserInterface\Web\UseCase;

use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[AsController]
#[Route(
    path: [
      '/',
      '/app/',
      '/app/{parameters}',
      '/app/{parameters}/{subParameters}',
    ],
    name: 'home',
    methods: ['GET'],
)]
final class IndexController extends AbstractController
{
    private Environment $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function __invoke(
        ?string $parameters,
        ?string $subParameters,
    ): Response {
        try {
            return new Response($this->environment->render('@UseCase/index.html.twig'));
        } catch (InvalidArgumentException $exception) {
            if (str_contains($exception->getMessage(), 'Could not find the entrypoints file from Webpack')) {
                return new Response('Building assets');
            }
            throw $exception;
        }
    }
}
