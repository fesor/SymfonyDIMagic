<?php

namespace Fesor\DIMagic\Tests\Fixtures\Services;

class DecoratedService implements ServiceInterface
{
    /**
     * @var ServiceInterface
     */
    private $next;

    /**
     * DecoratedService constructor.
     * @param ServiceInterface $next
     */
    public function __construct(ServiceInterface $next)
    {
        $this->next = $next;
    }
}
