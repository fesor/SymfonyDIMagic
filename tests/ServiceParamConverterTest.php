<?php

namespace Fesor\DIMagic\Tests;

use Fesor\DIMagic\DependencyInjection\ServiceResolver;
use Fesor\DIMagic\ParamConverter\ServiceParamConverter;
use PHPUnit\Framework\TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class ServiceParamConverterTest extends TestCase
{
    private $serviceResolver;
    /** @var  ServiceParamConverter */
    private $serviceParamConverter;

    public function setUp()
    {
        $this->serviceResolver = $this->createMock(ServiceResolver::class);
        $this->serviceParamConverter = new ServiceParamConverter($this->serviceResolver);
    }

    /**
     * @dataProvider supportsParamProvider
     */
    public function test_supports_only_params_for_which_service_could_be_provided($className, $stub, $expected, $message)
    {

        if (null !== $stub) {
            $this->serviceResolver
                ->expects(self::once())
                ->method('has')
                ->with($className)
                ->willReturn($stub);
        }

        self::assertEquals(
            $expected,
            $this->serviceParamConverter->supports($this->configuration($className)),
            $message
        );
    }

    public function test_apply_parameter()
    {
        $this->serviceResolver
            ->expects(self::once())
            ->method('get')
            ->with('Foo')
            ->willReturn('service');

        $request = new Request();
        $this->serviceParamConverter->apply($request, $this->configuration());

        self::assertEquals('service', $request->attributes->get('foo'));
    }

    public function supportsParamProvider()
    {
        return [
            [
                'Foo', false, false,
                'ServiceParamConverter should not support params with type for which service cannot be found'
            ],
            [
                'Bar', true, true,
                'ServiceParamConverter should support params with type for which service can be found'
            ],
            [
                null, null, false,
                'ServiceParamConverter should not support params without type'
            ]
        ];
    }

    private function configuration($className = 'Foo')
    {
        return new ParamConverter([
            'name' => 'foo',
            'class' => $className
        ]);
    }
}