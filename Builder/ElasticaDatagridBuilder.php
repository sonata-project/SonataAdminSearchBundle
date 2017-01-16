<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminSearchBundle\Builder;

use FOS\ElasticaBundle\Configuration\ManagerInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\AdminBundle\Builder\DatagridBuilderInterface;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Filter\FilterFactoryInterface;
use Sonata\AdminBundle\Guesser\TypeGuesserInterface;
use Sonata\AdminSearchBundle\Datagrid\Datagrid;
use Sonata\AdminSearchBundle\Datagrid\Pager;
use Sonata\AdminSearchBundle\Model\FinderProviderInterface;
use Sonata\AdminSearchBundle\ProxyQuery\ElasticaProxyQuery;
use Symfony\Component\Form\FormFactoryInterface;

class ElasticaDatagridBuilder implements DatagridBuilderInterface
{
    private $finderProvider;
    private $formFactory;
    private $filterFactory;
    private $guesser;
    private $configManager;

    public function __construct(FormFactoryInterface $formFactory, FilterFactoryInterface $filterFactory, TypeGuesserInterface $guesser, FinderProviderInterface $finderProvider, ManagerInterface $configManager)
    {
        $this->formFactory = $formFactory;
        $this->filterFactory = $filterFactory;
        $this->guesser = $guesser;
        $this->finderProvider = $finderProvider;
        $this->configManager = $configManager;
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
    public function addFilter(DatagridInterface $datagrid, $type, FieldDescriptionInterface $fieldDescription, AdminInterface $admin)
    {
        // Try to wrap all types to search types
        if ($type == null) {
            $guessType = $this->guesser->guessType($admin->getClass(), $fieldDescription->getName(), $admin->getModelManager());
            $type = $guessType->getType();
            $fieldDescription->setType($type);
            $options = $guessType->getOptions();

            foreach ($options as $name => $value) {
                if (is_array($value)) {
                    $fieldDescription->setOption($name, array_merge($value, $fieldDescription->getOption($name, array())));
                } else {
                    $fieldDescription->setOption($name, $fieldDescription->getOption($name, $value));
                }
            }
        } else {
            $fieldDescription->setType($type);
        }
        $this->fixFieldDescription($admin, $fieldDescription);
        $admin->addFilterFieldDescription($fieldDescription->getName(), $fieldDescription);

        $fieldDescription->mergeOption('field_options', array('required' => false));
        $filter = $this->filterFactory->create($fieldDescription->getName(), $type, $fieldDescription->getOptions());

        if (false !== $filter->getLabel() && !$filter->getLabel()) {
            $filter->setLabel($admin->getLabelTranslatorStrategy()->getLabel($fieldDescription->getName(), 'filter', 'label'));
        }

        $datagrid->addFilter($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseDatagrid(AdminInterface $admin, array $values = array())
    {
        $pager = new Pager();

        $defaultOptions = array();
        $defaultOptions['csrf_protection'] = false;

        $formBuilder = $this->formFactory->createNamedBuilder(
            'filter',
            'form',
            array(),
            $defaultOptions
        );

        $proxyQuery = $admin->createQuery();
        // if the default modelmanager query builder is used, we need to replace it with elastica
        // if not, that means $admin->createQuery has been overriden by the user and already returns an ElasticaProxyQuery object
        if (!$proxyQuery instanceof ElasticaProxyQuery) {
            if ($this->isSmart($admin, $values)) {
                $proxyQuery = new ElasticaProxyQuery($this->finderProvider->getFinderByAdmin($admin));
            }
        }

        return new Datagrid(
            $proxyQuery,
            $admin->getList(),
            $pager,
            $formBuilder,
            $values
        );
    }

    /**
     * Returns true if this datagrid builder can process these values.
     */
    public function isSmart(AdminInterface $admin, array $values = array())
    {

        // first : validate if elastica is asked in the configuration for this action
        $logicalControllerName = $admin->getRequest()->attributes->get('_controller');
        $currentAction = explode(':', $logicalControllerName);
        // remove Action from 'listAction'
        $currentAction = substr(end($currentAction), 0, -strlen('Action'));
        // in case of batch|export action, no need to elasticsearch
        if (!in_array($currentAction, $this->finderProvider->getActionsByAdmin($admin))) {
            return false;
        }

        // Get mapped field names
        $finderId = $this->finderProvider->getFinderIdByAdmin($admin);

        // Assume that finder id is composed like this 'fos_elastica.finder.<index name>.<type name>
        list($indexName, $typeName) = array_slice(explode('.', $finderId), 2);
        $typeConfiguration = $this->configManager->getTypeConfiguration($indexName, $typeName);
        $mapping = $typeConfiguration->getMapping();
        $mappedFieldNames = array_keys($mapping['properties']);

        // Compare to the fields on wich the search apply
        $smart = true;

        foreach ($values as $key => $value) {
            if (!is_array($value) || !isset($value['value'])) {
                // This is not a filter field
                continue;
            }

            if (!$value['value']) {
                // No value set on the filter field
                continue;
            }

            if (!in_array($key, $mappedFieldNames)) {
                /*
                 * We are in the case where a field is used as filter
                 * without being mapped in elastic search.
                 * An ugly case would be to have a custom field used in the filter
                 * without mapping in the model, we need to control that
                 */
                $ret = $admin->getModelManager()->getParentMetadataForProperty(
                    $admin->getClass(),
                    $key,
                    $admin->getModelManager()
                );

                list($metadata, $propertyName, $parentAssociationMappings) = $ret;
                //Case if a filter is used in the filter but not linked to the ModelManager ("mapped" = false ) case
                if (!$metadata->hasField($key)) {
                    break;
                }
                // This filter field is not mapped in elasticsearch
                // so we cannot use elasticsearch
                $smart = false;
                break;
            }
        }

        return $smart;
    }
}
