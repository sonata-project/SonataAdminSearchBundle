<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminSearchBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Finds all admin finder services definition and add a datagrid builder
 * injection.
 */
class DatagridBuilderInjectionCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('sonata.admin.search.admin_finder_services')) {
            return;
        }
        $adminFinderServices = $container->getParameter(
            'sonata.admin.search.admin_finder_services'
        );

        // Keep a trace of datagrid builder for each admin
        $originalAdminDatagridBuilders = [];

        foreach ($adminFinderServices as $adminId => $finderServiceId) {
            $definition = $container->getDefinition($adminId);

            foreach ($definition->getMethodCalls() as $call) {
                if ('setDatagridBuilder' !== $call[0]) {
                    continue;
                }

                $originalAdminDatagridBuilders[$adminId] = $call[1][0];
            }

            $definition->addMethodCall(
                'setDatagridBuilder',
                [new Reference('sonata.admin.search.datagrid_builder')]
            );
        }

        // Update definition of AdminSearchBundle Datagrid Builder
        $definition = $container->getDefinition('sonata.admin.search.datagrid_builder');
        $definition->replaceArgument(1, $originalAdminDatagridBuilders);
    }
}
