<?php

namespace Fesor\DIMagic\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceResolver
{
    private $container;

    private $serviceTypeMap;

    public function __construct(ContainerInterface $container, array $serviceTypeMap)
    {
        $this->container = $container;
        $this->serviceTypeMap = $serviceTypeMap;
    }

    /**
     * Checks is there any service with given type
     *
     * @param string $type
     * @return bool
     */
    public function has($type)
    {
        return isset($this->serviceTypeMap[$type]);
    }

    /**
     * Returns service by it's type
     *
     * @param string $type of a service
     * @return mixed service
     */
    public function get($type)
    {
        if (!$this->has($type)) {
            throw new \InvalidArgumentException('Service not found');
        }

        return $this->container->get($this->serviceTypeMap[$type]);
    }
}
