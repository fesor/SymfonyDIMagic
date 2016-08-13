<?php

namespace Fesor\DIMagic\ParamConverter;

use Fesor\DIMagic\DependencyInjection\ServiceResolver;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class ServiceParamConverter implements ParamConverterInterface
{
    private $serviceResolver;

    /**
     * ServiceParamConverter constructor.
     * @param ServiceResolver $serviceResolver
     */
    public function __construct(ServiceResolver $serviceResolver)
    {
        $this->serviceResolver = $serviceResolver;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $request->attributes->set(
            $configuration->getName(),
            $this->serviceResolver->get($configuration->getClass())
        );
    }

    public function supports(ParamConverter $configuration)
    {
        $className = $configuration->getClass();

        return null !== $className && $this->serviceResolver->has($className);
    }
}
