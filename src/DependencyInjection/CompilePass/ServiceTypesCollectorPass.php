<?php

namespace Fesor\DIMagic\DependencyInjection\CompilePass;

use Fesor\DIMagic\DependencyInjection\ServiceResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

class ServiceTypesCollectorPass implements CompilerPassInterface
{
    private $serviceTypeMap;

    public function __construct()
    {
        $this->serviceTypeMap = [];
    }

    public function process(ContainerBuilder $container)
    {
        $definitions = $container->getDefinitions();
        foreach ($definitions as $id => $definition) {
            $this->processDefinition($id, $definition);
        }

        $this->registerServiceResolver($container);
    }

    private function processDefinition(string $id, Definition $definition)
    {
        if (
            $definition->isSynthetic() || !$definition->isPublic()
            || $definition->isAbstract() || null !== $definition->getDecoratedService()
        ) {
            return;
        }

        $className = $definition->getClass();
        if (null !== $className) {
            $this->serviceTypeMap[$className] = $id;
        }
    }

    private function registerServiceResolver(ContainerBuilder $container)
    {
        $container->setParameter('di_magic.service_type_map', $this->serviceTypeMap);

        $container
            ->register('di_magic.service_resolver', ServiceResolver::class)
            ->addArgument(new Reference('service_container'))
            ->addArgument(new Parameter('di_magic.service_type_map'))
        ;
    }
}
