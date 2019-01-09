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

namespace Sonata\AdminSearchBundle\Model;

use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FinderProvider implements FinderProviderInterface
{
    protected $container;
    protected $adminFinderServices; // admin_id => array(finder)

    /**
     * @param ContainerInterface $container
     * @param array              $adminFinderServices
     */
    public function __construct(ContainerInterface $container, array $adminFinderServices)
    {
        $this->container = $container;
        $this->adminFinderServices = $adminFinderServices;
    }

    public function getFinderByAdmin(AdminInterface $admin)
    {
        return $this->getFinderByAdminId($admin->getCode());
    }

    public function getFinderByAdminId($adminId)
    {
        return $this->container->get($this->adminFinderServices[$adminId]['finder']);
    }

    public function getFinderIdByAdmin(AdminInterface $admin)
    {
        return $this->getFinderIdByAdminId($admin->getCode());
    }

    public function getFinderIdByAdminId($adminId)
    {
        return $this->adminFinderServices[$adminId]['finder'];
    }

    public function getActionsByAdmin(AdminInterface $admin)
    {
        return $this->getActionsByAdminId($admin->getCode());
    }

    public function getActionsByAdminId($adminId)
    {
        return $this->adminFinderServices[$adminId]['actions'];
    }
}
