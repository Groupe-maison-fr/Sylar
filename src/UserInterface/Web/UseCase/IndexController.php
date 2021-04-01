<?php

declare(strict_types=1);

namespace App\UserInterface\Web\UseCase;

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
        return new Response($this->environment->render('@UseCase/index.html.twig'));
    }
}
