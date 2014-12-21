<?php

/*
 * This file is part of the Sonata package.
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
     */
    public function __construct(
        ContainerInterface $container,
        array $adminFinderServices
    ) {
        $this->container           = $container;
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
}
