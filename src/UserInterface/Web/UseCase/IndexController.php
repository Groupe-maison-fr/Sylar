<?php

declare(strict_types=1);

namespace App\UserInterface\Web\UseCase;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class IndexController
{
    private Environment $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    public function index(): Response
    {
        try {
            return new Response($this->environment->render('@UseCase/index.html.twig'));
        } catch (InvalidArgumentException $exception) {
            if (preg_match('!Could not find the entrypoints file from Webpack!', $exception->getMessage())) {
                return new Response('Building assets');
            }
            throw $exception;
        }
    }
}
