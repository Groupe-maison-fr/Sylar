<?php

declare(strict_types=1);

namespace Tests\UserInterface\GraphQL\ServiceCloner;

use Tests\GraphQlTestCase;

/**
 * @internal
 */
class ServiceListGraphQlTest extends GraphQlTestCase
{
    public function testItShouldGetServices(): void
    {
        $result = $this->graphQlQuery(
            <<<GRAPHQL
                  query Services {
                    services {
                      name
                      image
                      command
                      labels {
                        name
                        value
                      }
                      environments {
                        name
                        value
                      }
                      ports {
                        containerPort
                        hostPort
                        hostIp
                      }
                    }
                  }
                GRAPHQL
        );
        $this->assertMatchesPattern(
            [
                'data' => [
                    'services' => [[
                        'name' => 'mysql',
                        'image' => 'library/mariadb:10.5.13',
                        'command' => '',
                        'labels' => [
                            [
                                'name' => 'labelName',
                                'value' => 'labelValue',
                            ]],
                        'ports' => [
                            [
                                'containerPort' => '3306/tcp',
                                'hostPort' => '=(33306+containerParameter.getIndex())~"/tcp"',
                                'hostIp' => '0.0.0.0',
                            ],
                        ],
                        'environments' => [
                            [
                                'name' => 'MYSQL_ROOT_PASSWORD',
                                'value' => 'sylar_root_password',
                            ],
                            [
                                'name' => 'MYSQL_USER',
                                'value' => 'sylar_user',
                            ],
                            [
                                'name' => 'MYSQL_PASSWORD',
                                'value' => 'sylar_password',
                            ],
                            [
                                'name' => 'MYSQL_INITDB_SKIP_TZINFO',
                                'value' => '1',
                            ],
                            [
                                'name' => 'CLONE_NAME',
                                'value' => '=containerParameter.getName()',
                            ],
                            [
                                'name' => 'CLONE_INDEX',
                                'value' => '=containerParameter.getIndex()',
                            ],
                            [
                                'name' => 'CLONE_REPLICATED_FILESYSTEM',
                                'value' => '=containerParameter.getReplicatedFilesystem()',
                            ],
                        ],
                    ], [
                        'name' => 'postgresql',
                        'image' => 'postgres:14.8-alpine3.18',
                        'command' => '',
                        'labels' => '@array@',
                        'ports' => '@array@',
                        'environments' => '@array@',
                    ]],
                ],
            ],
            $result,
        );
    }
}
