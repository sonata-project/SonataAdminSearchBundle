<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminSearchBundle\Builder;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\AdminBundle\Builder\DatagridBuilderInterface;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminSearchBundle\Datagrid\Datagrid;

/**
 * Admin search bundle wraps existing datagrid builder (orm, odm, phpcr)
 * and provides efficient datagrid builder based on smart engine (elasticsearch, )
 * Some filter fields could not be stored in the smart engine so we have to fallback
 * on the original datagrid builder (orm, odm, phpcr).
 */
class DatagridBuilder implements DatagridBuilderInterface
{
    private $smartDatagridBuilder; // FIXME: Assume the default one is based on elasticsearch
    private $originalAdminDatagridBuilders; // For each admin, keep a reference to the original datagrid builder

    public function __construct(DatagridBuilderInterface $smartDatagridBuilder, $originalAdminDatagridBuilders = array())
    {
        $this->smartDatagridBuilder          = $smartDatagridBuilder;
        $this->originalAdminDatagridBuilders = $originalAdminDatagridBuilders;
    }

    private function getAdminDatagridBuilder($admin, $smartDatagrid = true)
    {
        if ($smartDatagrid) {
            return $this->smartDatagridBuilder;
        }

        // Search the original datagrid builder for the specified admin
        $datagridBuilder = $this->originalAdminDatagridBuilders[$admin->getCode()];

        return $datagridBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function fixFieldDescription(AdminInterface $admin, FieldDescriptionInterface $fieldDescription)
    {
        // Nothing todo
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(DatagridInterface $datagrid, $type = null, FieldDescriptionInterface $fieldDescription, AdminInterface $admin)
    {
        return $this->getAdminDatagridBuilder($admin, $datagrid instanceof Datagrid)->addFilter($datagrid, $type, $fieldDescription, $admin);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseDatagrid(AdminInterface $admin, array $values = array())
    {
        // Check if we use smart or original datagrid builder
        $smartDatagrid = $this->smartDatagridBuilder->isSmart($admin, $values);

        return $this->getAdminDatagridBuilder($admin, $smartDatagrid)->getBaseDatagrid($admin, $values);
    }
}
