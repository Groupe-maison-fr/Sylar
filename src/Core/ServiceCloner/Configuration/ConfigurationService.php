<?php

declare(strict_types=1);

namespace App\Core\ServiceCloner\Configuration;

use App\Core\ServiceCloner\Configuration\Object\Service;
use App\Core\ServiceCloner\Configuration\Object\ServiceCloner;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Yaml\Yaml;

final class ConfigurationService implements ConfigurationServiceInterface
{
    private string $configurationFilename;
    private string $mountedConfigurationPath;
    private string $containerConfigurationPath;

    public function __construct(
        string $configurationFilename,
        string $mountedConfigurationPath,
        string $containerConfigurationPath,
    ) {
        $this->configurationFilename = $configurationFilename;
        $this->mountedConfigurationPath = $mountedConfigurationPath;
        $this->containerConfigurationPath = $containerConfigurationPath;
    }

    public function getConfiguration(): ServiceCloner
    {
        chdir($this->containerConfigurationPath);

        $processor = new Processor();

        return $this->createServiceClonerFromArray(
            array_merge(
                $processor->processConfiguration(
                    new TreeBuilderConfiguration(),
                    [Yaml::parse(
                        file_get_contents($this->configurationFilename),
                    )],
                ),
                [
                'configurationRoot' => $this->mountedConfigurationPath,
            ],
            ),
        );
    }

    private function getSerializer(): Serializer
    {
        return new Serializer([
            new ArrayDenormalizer(),
            new DateTimeNormalizer(),
            new ObjectNormalizer(
                new ClassMetadataFactory(
                    new AnnotationLoader(
                        new AnnotationReader(),
                    ),
                ),
                null,
                null,
                new PhpDocExtractor(),
            ),
        ], [
            new JsonEncoder(),
        ]);
    }

    public function createServiceClonerFromArray(array $data): ServiceCloner
    {
        /** @var ServiceCloner */
        return $this->getSerializer()->denormalize($data, ServiceCloner::class, 'array');
    }

    public function createServiceFromArray(array $data): Service
    {
        /** @var Service */
        return $this->getSerializer()->denormalize($data, Service::class, 'array');
    }
}
