<?php

namespace Fesor\DIMagic\Tests;

use Fesor\DIMagic\DependencyInjection\CompilePass\ServiceTypesCollectorPass;
use Fesor\DIMagic\Tests\Fixtures\Services\DecoratedService;
use Fesor\DIMagic\Tests\Fixtures\Services\Service;
use Fesor\DIMagic\Tests\Fixtures\Services\ServiceInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ServiceTypeResolverCompilePassTest extends \PHPUnit\Framework\TestCase
{
    /** @var  ContainerBuilder */
    private $containerBuilder;
    /** @var  ServiceTypesCollectorPass */
    private $pass;

    public function setUp()
    {
        $this->containerBuilder = new ContainerBuilder();
        $this->pass = new ServiceTypesCollectorPass();
    }

    public function testProcessingPublicServices()
    {
        $this->containerBuilder
            ->register('service', Service::class);

        $this->pass->process($this->containerBuilder);

        $this->assertEquals([
            Service::class => 'service',
        ], $this->serviceTypeMap(), 'Class map should contain one public service including it\'s interfaces');
    }

    public function testProcessingDefinitionsForServicesThatNotAllowedToInstantiate()
    {
        $this->containerBuilder
            ->register('service', Service::class)
            ->setPublic(false);

        $this->containerBuilder
            ->register('service_synthetic', Service::class)
            ->setSynthetic(true);

        $this->containerBuilder
            ->register('abstract_service', ServiceInterface::class)
            ->setAbstract(true);

        $this->pass->process($this->containerBuilder);

        $this->assertEquals([], $this->serviceTypeMap(), 'Class map should be empty');
    }

    public function testProcessingDecoratedServices()
    {
        $this->containerBuilder
            ->register('service', Service::class);
        $this->containerBuilder
            ->register('service_decorator', DecoratedService::class)
            ->setDecoratedService('service')
            ->addArgument(new Reference('service_decorator.inner'));

        $this->pass->process($this->containerBuilder);

        $this->assertEquals([
            Service::class => 'service',
        ], $this->serviceTypeMap(), 'There should be only decorating service in the map including it\'s interfaces');
    }

    private function serviceTypeMap()
    {
        return $this->containerBuilder->getParameter('di_magic.service_type_map');
    }
}
