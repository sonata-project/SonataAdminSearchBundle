<?php

namespace Sonata\AdminSearchBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sonata\AdminSearchBundle\DependencyInjection\Compiler\DatagridBuilderInjectionCompilerPass;

class SonataAdminSearchBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DatagridBuilderInjectionCompilerPass());
    }
}
