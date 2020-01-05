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

namespace Sonata\AdminSearchBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Sonata\AdminSearchBundle\DependencyInjection\Compiler\DatagridBuilderInjectionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class DatagridBuilderInjectionCompilerPassTest extends AbstractCompilerPassTestCase
{
    public function testDatagridBuilderIsInjected()
    {
        $this->setParameter(
            'sonata.admin.search.admin_finder_services',
            ['my_admin' => ['finder' => 'my_finder_service']]
        );
        $this->setDefinition('my_admin', new Definition());
        $this->setDefinition('sonata.admin.search.elastica_datagrid_builder', new Definition());
        $this->setDefinition(
            'sonata.admin.search.datagrid_builder',
            new Definition(
                'Sonata\AdminSearchBundle\Builder\DatagridBuilder',
                [
                    new Reference('sonata.admin.search.elastica_datagrid_builder'),
                    null,
                ]
            )
        );
        $this->compile();
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'my_admin',
            'setDatagridBuilder',
            [new Reference('sonata.admin.search.datagrid_builder')]
        );
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new DatagridBuilderInjectionCompilerPass());
    }
}
