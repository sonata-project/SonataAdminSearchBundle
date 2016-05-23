<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminSearchBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\AdminSearchBundle\DependencyInjection\SonataAdminSearchExtension;

class SonataAdminSearchExtensionTest extends AbstractExtensionTestCase
{
    public function getContainerExtensions()
    {
        return array(
            new SonataAdminSearchExtension(),
        );
    }

    public function testLoad()
    {
        $this->load(array(
            'admin_finder_services' => $expectedParameterValue = array(
                'my_id' => array(
                    'finder' => 'test',
                    'actions' => array('list'),
                ),
            ),
        ));
        $this->assertContainerBuilderHasParameter(
            'sonata.admin.search.admin_finder_services',
            $expectedParameterValue
        );

        $this->assertContainerBuilderHasService(
            'sonata.admin.search.finder_provider'
        );
    }
}
