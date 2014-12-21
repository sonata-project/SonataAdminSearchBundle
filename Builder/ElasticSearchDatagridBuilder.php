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

use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Builder\DatagridBuilderInterface;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Sonata\AdminBundle\Datagrid\Datagrid;
use Sonata\AdminSearchBundle\Model\FinderProviderInterface;
use Sonata\AdminSearchBundle\ProxyQuery\ElasticaProxyQuery;
use Elastica\Query;
use Sonata\AdminSearchBundle\Datagrid\Pager;
use Elastica\Query\Builder;

class ElasticSearchDatagridBuilder implements DatagridBuilderInterface
{
    /**
     * For the moment, we assume elasticsearch is used on top of another system.
     * We let part of the job be done by the underlying implementation
     */
    private $databaseDatagridBuilder;
    private $finderProvider;
    private $formFactory;

    public function __construct(
        DatagridBuilderInterface $databaseDatagridBuilder,
        FormFactoryInterface $formFactory,
        FinderProviderInterface $finderProvider
    ) {
        $this->databaseDatagridBuilder = $databaseDatagridBuilder;
        $this->formFactory             = $formFactory;
        $this->finderProvider          = $finderProvider;
    }

    /**
     * proxy for the underlying datagrid builder
     *
     * @param \Sonata\AdminBundle\Admin\AdminInterface            $admin
     * @param \Sonata\AdminBundle\Admin\FieldDescriptionInterface $fieldDescription
     *
     * @return void
     */
    public function fixFieldDescription(
        AdminInterface $admin,
        FieldDescriptionInterface $fieldDescription
    ) {
        $this->databaseDatagridBuilder->fixFieldDescription(
            $admin,
            $fieldDescription
        );
    }

    /**
     * proxy for the underlying datagrid builder
     *
     * @param \Sonata\AdminBundle\Datagrid\DatagridInterface      $datagrid
     * @param null                                                $type
     * @param \Sonata\AdminBundle\Admin\FieldDescriptionInterface $fieldDescription
     * @param \Sonata\AdminBundle\Admin\AdminInterface            $admin
     */
    public function addFilter(
        DatagridInterface $datagrid,
        $type = null,
        FieldDescriptionInterface $fieldDescription,
        AdminInterface $admin
    ) {
        return $this->databaseDatagridBuilder->addFilter(
            $datagrid,
            $type,
            $fieldDescription,
            $admin
        );
    }

    /**
     * @param \Sonata\AdminBundle\Admin\AdminInterface $admin
     * @param array                                    $values
     *
     * @return \Sonata\AdminBundle\Datagrid\DatagridInterface
     */
    public function getBaseDatagrid(
        AdminInterface $admin,
        array $values = array()
    ) {
        $pager = new Pager();

        $defaultOptions = array();
        $defaultOptions['csrf_protection'] = false;

        $formBuilder = $this->formFactory->createNamedBuilder(
            'filter',
            'form',
            array(),
            $defaultOptions
        );

        $proxyQuery = new ElasticaProxyQuery(
            new Builder(), //query builder
            $this->finderProvider->getFinderByAdmin($admin)
        );

        return new Datagrid(
            $proxyQuery,
            $admin->getList(),
            $pager,
            $formBuilder,
            $values
        );
    }
}
