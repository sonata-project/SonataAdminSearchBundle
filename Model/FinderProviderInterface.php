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

interface FinderProviderInterface
{
    /**
     * @param Sonata\AdminBundle\Admin\AdminInterface $admin Sonata Admin interface
     *
     * @return FOS\ElasticaBundle\Finder\PaginatedFinderInterface
     */
    public function getFinderByAdmin(AdminInterface $admin);

    /**
     * @param string $adminId Sonata Admin service id
     *
     * @return FOS\ElasticaBundle\Finder\PaginatedFinderInterface
     */
    public function getFinderByAdminId($adminId);

    /**
     * @param Sonata\AdminBundle\Admin\AdminInterface $admin Sonata Admin interface
     *
     * @return string
     */
    public function getFinderIdByAdmin(AdminInterface $admin);

    /**
     * @param string $adminId Sonata Admin service id
     *
     * @return string
     */
    public function getFinderIdByAdminId($adminId);
}
