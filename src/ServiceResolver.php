<?php

namespace Fesor\DIMagic;


use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceResolver
{
    private $container;

    private $serviceTypeMap;

    /**
     * ServiceResolver constructor.
     * @param ContainerInterface $container
     * @param array $serviceTypeMap
     */
    public function __construct(ContainerInterface $container, array $serviceTypeMap)
    {
        $this->container = $container;
        $this->serviceTypeMap = $serviceTypeMap;
    }

    public function has($className)
    {
        return isset($this->serviceTypeMap[$className]);
    }

    public function get($className)
    {
        if (!$this->has($className)) {
            throw new \InvalidArgumentException('Service not found');
        }

        return $this->container->get($this->serviceTypeMap[$className]);
    }
}
