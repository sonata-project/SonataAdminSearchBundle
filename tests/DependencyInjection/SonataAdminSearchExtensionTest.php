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

namespace Sonata\AdminSearchBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sonata\AdminSearchBundle\DependencyInjection\SonataAdminSearchExtension;

class SonataAdminSearchExtensionTest extends AbstractExtensionTestCase
{
    public function getContainerExtensions(): array
    {
        return [
            new SonataAdminSearchExtension(),
        ];
    }

    public function testLoad(): void
    {
        $this->load([
            'admin_finder_services' => $expectedParameterValue = [
                'my_id' => [
                    'finder' => 'test',
                    'actions' => ['list'],
                ],
            ],
        ]);
        $this->assertContainerBuilderHasParameter(
            'sonata.admin.search.admin_finder_services',
            $expectedParameterValue
        );

        $this->assertContainerBuilderHasService(
            'sonata.admin.search.finder_provider'
        );
    }
}
