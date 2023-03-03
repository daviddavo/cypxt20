<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

use Sherlockode\ConfigurationBundle\Manager\ParameterManagerInterface;
use Sherlockode\ConfigurationBundle\Manager\UploadManagerInterface;

/**
 * Extensión de Twig que retorna el valor cxt_<valor> o pxt_<valor> dependiendo
 * de si estamos en el subdominio cxt o pxt.
 */
class CypxtConfig
{
    public function __construct(
        protected RequestStack $requestStack, 
        protected ParameterManagerInterface $pmi,
        protected UploadManagerInterface $umi,
    ) {}

    public function __call($method, $args): mixed
    {
        $host = $this->requestStack->getCurrentRequest()->getHost();
        $value = $this->pmi->get(
            (str_starts_with($host, 'cxt.')?'cxt':'pxt') . '__' . $method
        );

        if (isset($value['src'])) {
            $value = $this->umi->resolveUri($value['src']);
        }

        return $value;
    }
}