Filter field definition
=======================

Available filter types
----------------------

* `sonata_search_elastica_boolean`: depends on the ``sonata_type_filter_default`` Form Type, renders yes or no field,
* `sonata_search_elastica_callback`: depends on the ``sonata_type_filter_default`` Form Type,
* `sonata_search_elastica_choice`: depends on the ``sonata_type_filter_default`` Form Type,
* `sonata_search_elastica_string`: depends on the ``sonata_type_filter_choice`` Form Type,
* `sonata_search_elastica_number`: depends on the ``sonata_type_filter_number`` Form Type,
* `sonata_search_elastica_date` : depends on the ``sonata_type_filter_date`` From Type, renders a date field,
* `sonata_search_elastica_date_range` : depends on the ``sonata_type_filter_date_range`` From Type, renders a 2 date fields,
* `sonata_search_elastica_datetime` : depends on the ``sonata_type_filter_datetime`` From Type, renders a datetime field,
* `sonata_search_elastica_datetime_range` : depends on the ``sonata_type_filter_datetime_range`` From Type, renders a 2 datetime fields.

Callback
^^^^^^^^

To create a custom callback filter, you just need to set the "callback" filter option
to a valid callback function. First argument of this function will be
``Sonata\AdminSearchBundle\ProxyQuery\ElasticaProxyQuery`` instance which could be
modified according to your needs::

    namespace Sonata\NewsBundle\Admin;

    Sonata\AdminBundle\Admin\AbstractAdmin;
    use Sonata\AdminBundle\Datagrid\DatagridMapper;
    use Sonata\AdminSearchBundle\ProxyQuery\ElasticaProxyQuery;

    final class PostAdmin extends AbstractAdmin
    {
        protected function configureDatagridFilters(DatagridMapper $datagridMapper)
        {
            $datagridMapper
                ->add('title')
                ->add('name', Sonata\AdminSearchBundle\Filter\CallbackFilter::class, [
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
                ])
            ;
        }
    }

Date
^^^^

To make query on date/datetime type, you can use one of the `sonata_search_elastica_date`
filter types. For example if you have a date in the ISO 8601 date format::

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('date', Sonata\AdminSearchBundle\Filter\DateTimeFilter::class, null, 'datetime', [
                'format' => 'c',
            ])
        ;
    }

The format must be a string formatted according to the `php format date`_ and be the one used
to map the data in elasticsearch. If it is not the same, ElasticSearch will raise an exception
``failed to parse date field [15/05/28] Invalid format``.

.. _php format date: http://php.net/manual/en/function.date.php
