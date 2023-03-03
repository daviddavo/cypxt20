<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CypxtParams
{
    public function __construct(protected RequestStack $requestStack, protected ContainerInterface $container) {}

    public function __call($method, $args): string
    {
        $host = $this->requestStack->getCurrentRequest()->getHost();
        $cypxt_params = $this->container->getParameter(str_starts_with($host, 'cxt.')?'app.cxt':'app.pxt');
        return $cypxt_params[$method];
    }
}