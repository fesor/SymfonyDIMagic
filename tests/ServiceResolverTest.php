<?php

namespace Fesor\DIMagic\Tests;

use Fesor\DIMagic\DependencyInjection\ServiceResolver;
use Fesor\DIMagic\Tests\Fixtures\Services\DecoratedService;
use Fesor\DIMagic\Tests\Fixtures\Services\Service;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceResolverTest extends TestCase
{
    private $container;
    /** @var  ServiceResolver */
    private $serviceResolver;

    public function setUp()
    {
        $this->container = $this->createMock(ContainerInterface::class);

        $this->serviceResolver = new ServiceResolver($this->container, [
            Service::class => 'service'
        ]);
    }

    public function testGettingService()
    {
        $this->container->expects($this->once())->method('get')->with('service')->willReturnArgument(0);
        $this->serviceResolver->get(Service::class);
    }

    public function testGettingNotRegisteredService()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->serviceResolver->get(DecoratedService::class);
    }
}
