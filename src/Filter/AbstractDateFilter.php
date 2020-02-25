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

namespace Sonata\AdminSearchBundle\Filter;

use Elastica\QueryBuilder;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\Type\Filter\DateRangeType;
use Sonata\AdminBundle\Form\Type\Filter\DateTimeType;
use Sonata\AdminBundle\Form\Type\Operator\DateOperatorType;
use Sonata\AdminBundle\Form\Type\Operator\DateRangeOperatorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as SymfonyDateTimeType;

abstract class AbstractDateFilter extends Filter
{
    /**
     * Flag indicating that filter will have range.
     *
     * @var bool
     *
     * NEXT_MAJOR: Remove this property
     *
     * @deprecated since 1.1, will be removed in 2.0.
     */
    protected $range = false;

    /**
     * Flag indicating that filter will filter by datetime instead by date.
     *
     * @var bool
     *
     * NEXT_MAJOR: Remove this property
     *
     * @deprecated since 1.1, will be removed in 2.0.
     */
    protected $time = false;

    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $query, $alias, $field, $data)
    {
        // check data sanity
        if (!$data || !\is_array($data) || !\array_key_exists('value', $data)) {
            return;
        }

        $format = \array_key_exists('format', $this->getFieldOptions()) ? $this->getFieldOptions()['format'] : 'c';
        $queryBuilder = new QueryBuilder();

        /*
         * NEXT_MAJOR: Use ($this instanceof RangeFilterInterface) for if statement, remove deprecated range.
         */
        if (!($range = $this instanceof RangeFilterInterface)) {
            @trigger_error(
                sprintf(
                    'Using `range` property is deprecated since version 1.x, will be removed in 2.0.'.
                    ' Implement %s instead.',
                    RangeFilterInterface::class
                ),
                E_USER_DEPRECATED
            );

            $range = $this->range;
        }

        if ($range) {
            // additional data check for ranged items
            if (!\array_key_exists('start', $data['value']) || !\array_key_exists('end', $data['value'])) {
                return;
            }

            if (!$data['value']['start'] || !$data['value']['end']) {
                return;
            }

            // transform types
            if ('timestamp' === $this->getOption('input_type')) {
                $data['value']['start'] = $data['value']['start'] instanceof \DateTime ? $data['value']['start']->getTimestamp() : 0;
                $data['value']['end'] = $data['value']['end'] instanceof \DateTime ? $data['value']['end']->getTimestamp() : 0;
            }

            // default type for range filter
            $data['type'] = !isset($data['type']) || !is_numeric($data['type']) ? DateRangeType::TYPE_BETWEEN : $data['type'];

            $innerQuery = $queryBuilder
                ->query()
                ->range($field, [
                    'gte' => $data['value']['start']->format($format),
                    'lte' => $data['value']['end']->format($format),
                ])
            ;

            if (DateRangeOperatorType::TYPE_NOT_BETWEEN === $data['type']) {
                $query->addMustNot($innerQuery);
            } else {
                $query->addMust($innerQuery);
            }
        } else {
            if (!$data['value']) {
                return;
            }

            // default type for simple filter
            $data['type'] = !isset($data['type']) || !is_numeric($data['type']) ? DateOperatorType::TYPE_GREATER_EQUAL : $data['type'];
            // just find an operator and apply query
            $operator = $this->getOperator($data['type']);

            // transform types
            if ('timestamp' === $this->getOption('input_type')) {
                $data['value'] = $data['value'] instanceof \DateTime ? $data['value']->getTimestamp() : 0;
            }

            // null / not null only check for col
            if (\in_array($operator, ['missing', 'exists'], true)) {
                $innerQuery = $queryBuilder
                    ->query()
                    ->exists($field);
            } elseif ('=' === $operator) {
                $innerQuery = $queryBuilder
                    ->query()
                    ->range($field, [
                        'gte' => $data['value']->format($format),
                        'lte' => $data['value']->format($format),
                    ])
                ;
            } else {
                $innerQuery = $queryBuilder
                    ->query()
                    ->range($field, [
                        $operator => $data['value']->format($format),
                    ])
                ;
            }

            if ('missing' === $operator) {
                $query->addMustNot($innerQuery);
            } else {
                $query->addMust($innerQuery);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return [
            'input_type' => SymfonyDateTimeType::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderSettings()
    {
        return [
            $this->getFilterTypeClass(),
            [
                'field_type' => $this->getFieldType(),
                'field_options' => $this->getFieldOptions(),
                'label' => $this->getLabel(),
            ],
        ];
    }

    /**
     * @return string
     *
     * NEXT_MAJOR: Make this method abstract
     */
    protected function getFilterTypeClass()
    {
        @trigger_error(
            __METHOD__.' should be implemented. It will be abstract in 2.0.',
            E_USER_DEPRECATED
        );

        return DateTimeType::class;
    }

    /**
     * Resolves DataType:: constants to SQL operators.
     *
     * @param int $type
     *
     * @return string
     */
    protected function getOperator($type)
    {
        $type = (int) $type;

        $choices = [
            DateOperatorType::TYPE_EQUAL => '=',
            DateOperatorType::TYPE_GREATER_EQUAL => 'gte',
            DateOperatorType::TYPE_GREATER_THAN => 'gt',
            DateOperatorType::TYPE_LESS_EQUAL => 'lte',
            DateOperatorType::TYPE_LESS_THAN => 'lt',
            DateOperatorType::TYPE_NULL => 'missing',
            DateOperatorType::TYPE_NOT_NULL => 'exists',
        ];

        return $choices[$type] ?? '=';
    }
}
