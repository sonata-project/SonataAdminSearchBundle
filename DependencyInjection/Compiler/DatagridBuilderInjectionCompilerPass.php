<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminSearchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DatagridBuilderInjectionCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $adminFinderServices = $container->getParameter('sonata.admin.search.admin_finder_services');

        foreach ($adminFinderServices as $adminId=>$finderServiceId) {
            $definition = $container->getDefinition($adminId);
            $definition->addMethodCall('setDatagridBuilder', array(new Reference('sonata.admin.builder.orm_datagrid')));
        }
    }
}
