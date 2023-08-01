<?php

declare(strict_types=1);

namespace Tests;

use Coduo\PHPMatcher\PHPUnit\PHPMatcherAssertions;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @internal
 */
class GraphQlTestCase extends WebTestCase
{
    use IntegrationTestTrait;
    use PHPMatcherAssertions;

    /**
     * @var Closure(ContainerInterface)[]
     */
    private array $preGraphQLCallbacks = [];

    /**
     * @var Closure(ContainerInterface)[]
     */
    private array $postGraphQLCallbacks = [];

    private array $graphqlCallServices = [];

    protected bool $shouldThrowException = false;
    private array $errors;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    protected function createKernelBrowserClient(): KernelBrowser
    {
        static::ensureKernelShutdown();

        return static::createClient();
    }

    /**
     * @param callable(ContainerInterface) $callback
     */
    protected function graphQlAddPreCallback(callable $callback): void
    {
        $this->preGraphQLCallbacks[] = $callback;
    }

    /**
     * @param callable(ContainerInterface) $callback
     */
    protected function graphQlAddPostCallback(callable $callback): void
    {
        $this->postGraphQLCallbacks[] = $callback;
    }

    protected function graphQlSetService(string $id, mixed $service): void
    {
        $this->graphqlCallServices[$id] = $service;
    }

    public function graphQlQuery(string $payload, array $variables = []): array
    {
        return $this->graphQLRequest('query', $payload, $variables);
    }

    public function graphQlMutation(string $payload, array $variables = []): array
    {
        return $this->graphQLRequest('mutation', $payload, $variables);
    }

    /**
     * @param 'query|mutation' $requestType
     */
    private function graphQLRequest(string $requestType, string $payload, array $variables = []): array
    {
        $client = $this->createKernelBrowserClient();

        foreach ($this->preGraphQLCallbacks as $preGraphQLCallback) {
            $preGraphQLCallback($client->getContainer());
        }

        foreach ($this->graphqlCallServices as $serviceId => $service) {
            $client->getContainer()->set($serviceId, $service);
        }

        $client->jsonRequest('POST', '/graphql/', ['query' => $payload, 'variables' => $variables]);

        foreach ($this->postGraphQLCallbacks as $postGraphQLCallback) {
            $postGraphQLCallback($client->getContainer());
        }

        $content = $client->getInternalResponse()->getContent();
        if ($client->getResponse()->getStatusCode() !== 200) {
            throw new Exception(
                sprintf('ResponseCode is %s: [%s]:%s %s', $client->getResponse()->getStatusCode(), $requestType, $payload, $content),
            );
        }

        $graphqlQueryResult = json_decode($content, true);

        if (isset($graphqlQueryResult['errors'][0]['message'])) {
            if ($this->shouldThrowException && $graphqlQueryResult['errors'][0]['message'] === 'Access denied to this field.') {
                throw new AccessDeniedHttpException(json_encode($graphqlQueryResult['errors'][0]['path']));
            }

            $this->errors = $graphqlQueryResult['errors'];
        }

        return $graphqlQueryResult;
    }
}
