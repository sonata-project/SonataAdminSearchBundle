<?php

namespace Sonata\AdminSearchBundle;

use Sonata\AdminSearchBundle\DependencyInjection\Compiler\DatagridBuilderInjectionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

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
