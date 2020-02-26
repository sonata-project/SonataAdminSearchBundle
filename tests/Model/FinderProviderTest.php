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

namespace Sonata\AdminSearchBundle\Tests\Model;

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminSearchBundle\Model\FinderProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FinderProviderTest extends TestCase
{
    public function testGetFinderByAdmin(): void
    {
        $admin = $this->createMock(AdminInterface::class);
        $container = $this->createMock(ContainerInterface::class);
        $provider = new FinderProvider(
            $container,
            [
                $adminId = 'planet_admin' => [
                    'finder' => $finderId = 'fos_elastica.planet_finder',
                ],
            ]
        );
        $finder = new \StdClass();

        $container->expects($this->once())
            ->method('get')
            ->with($finderId)
            ->willReturn($finder);

        $admin->expects($this->once())
            ->method('getCode')
            ->willReturn($adminId);

        $this->assertSame(
            $finder,
            $provider->getFinderByAdmin($admin)
        );
    }
}
