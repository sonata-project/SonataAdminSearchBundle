.. index::

Filter field definition
=======================

Available filter types
----------------------

* `sonata_search_elastica_boolean`: depends on the ``sonata_type_filter_default`` Form Type, renders yes or no field,
* `sonata_search_elastica_callback`: depends on the ``sonata_type_filter_default`` Form Type,
* `sonata_search_elastica_choice`: depends on the ``sonata_type_filter_default`` Form Type,
* `sonata_search_elastica_string`: depends on the ``sonata_type_filter_choice`` Form Type,
* `sonata_search_elastica_number`: depends on the ``sonata_type_filter_number`` Form Type

Callback
^^^^^^^^

To create a custom callback filter, you just need to set the "callback" filter option
to a valid callback function. First argument of this function will be
``Sonata\AdminSearchBundle\ProxyQuery\ElasticaProxyQuery`` instance which could be
modified according to your needs.

.. code-block:: php

    <?php
    namespace Sonata\NewsBundle\Admin;

    use Sonata\AdminBundle\Admin\Admin;
    use Sonata\AdminBundle\Datagrid\DatagridMapper;
    use Sonata\AdminSearchBundle\ProxyQuery\ElasticaProxyQuery;

    class PostAdmin extends Admin
    {
        protected function configureDatagridFilters(DatagridMapper $datagridMapper)
        {
            $datagridMapper
                ->add('title')
                ->add('name', 'sonata_search_elastica_callback', array(
                    'callback' => function (ElasticaProxyQuery $query, $alias, $field, $data) {
                        if (!$data || !is_array($data) || !array_key_exists('value', $data)) {
                            return;
                        }

                        $queryBuilder = new \Elastica\Query\Builder();

                        $queryBuilder
                            ->fieldOpen('multi_match')
                                ->field('query', trim($data['value']))
                                ->field('fields', ['name', 'name.std'])
                                ->field('operator', 'and')
                            ->fieldClose();

                        $query->addMust($queryBuilder);
                    }
                ))
            ;
        }
    }
