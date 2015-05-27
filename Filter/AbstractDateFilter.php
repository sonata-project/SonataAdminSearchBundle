<?php

namespace Sonata\AdminSearchBundle\Filter;

use Sonata\AdminBundle\Form\Type\Filter\DateType;
use Sonata\AdminBundle\Form\Type\Filter\DateRangeType;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

abstract class AbstractDateFilter extends Filter
{
    /**
     * Flag indicating that filter will have range.
     *
     * @var bool
     */
    protected $range = false;

    /**
     * Flag indicating that filter will filter by datetime instead by date.
     *
     * @var bool
     */
    protected $time = false;

    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $query, $alias, $field, $data)
    {
        // check data sanity
        if (!$data || !is_array($data) || !array_key_exists('value', $data)) {
            return;
        }

        $format = array_key_exists('format', $this->getFieldOptions()) ? $this->getFieldOptions()['format'] : 'c';
        $queryBuilder = new \Elastica\Query\Builder();

        if ($this->range) {
            // additional data check for ranged items
            if (!array_key_exists('start', $data['value']) || !array_key_exists('end', $data['value'])) {
                return;
            }

            if (!$data['value']['start'] || !$data['value']['end']) {
                return;
            }

            // transform types
            if ($this->getOption('input_type') == 'timestamp') {
                $data['value']['start'] = $data['value']['start'] instanceof \DateTime ? $data['value']['start']->getTimestamp() : 0;
                $data['value']['end'] = $data['value']['end'] instanceof \DateTime ? $data['value']['end']->getTimestamp() : 0;
            }

            // default type for range filter
            $data['type'] = !isset($data['type']) || !is_numeric($data['type']) ? DateRangeType::TYPE_BETWEEN : $data['type'];

            $queryBuilder
                ->fieldOpen('range')
                    ->fieldOpen($field)
                        ->field('gte', $data['value']['start']->format($format))
                        ->field('lte', $data['value']['end']->format($format))
                    ->fieldClose()
                ->fieldClose();

            if ($data['type'] == DateRangeType::TYPE_NOT_BETWEEN) {
                $query->addMustNot($queryBuilder);
            } else {
                $query->addMust($queryBuilder);
            }
        } else {
            if (!$data['value']) {
                return;
            }

            // default type for simple filter
            $data['type'] = !isset($data['type']) || !is_numeric($data['type']) ? DateType::TYPE_GREATER_EQUAL : $data['type'];
            // just find an operator and apply query
            $operator = $this->getOperator($data['type']);

            // transform types
            if ($this->getOption('input_type') == 'timestamp') {
                $data['value'] = $data['value'] instanceof \DateTime ? $data['value']->getTimestamp() : 0;
            }

            // null / not null only check for col
            if (in_array($operator, array('missing', 'exists'))) {
                $queryBuilder
                    ->fieldOpen($operator)
                        ->field('field', $field)
                    ->fieldClose();
            } elseif ($operator == '=') {
                $queryBuilder
                    ->fieldOpen('range')
                        ->fieldOpen($field)
                          ->field('gte', $data['value']->format($format))
                          ->field('lte', $data['value']->format($format))
                      ->fieldClose()
                  ->fieldClose();
            } else {
                $queryBuilder
                    ->fieldOpen('range')
                        ->fieldOpen($field)
                            ->field($operator, $data['value']->format($format))
                        ->fieldClose()
                    ->fieldClose();
            }
            $query->addMust($queryBuilder);
        }
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
        $type = intval($type);

        $choices = array(
            DateType::TYPE_EQUAL => '=',
            DateType::TYPE_GREATER_EQUAL => 'gte',
            DateType::TYPE_GREATER_THAN => 'gt',
            DateType::TYPE_LESS_EQUAL => 'lte',
            DateType::TYPE_LESS_THAN => 'lt',
            DateType::TYPE_NULL => 'missing',
            DateType::TYPE_NOT_NULL => 'exists',
        );

        return isset($choices[$type]) ? $choices[$type] : '=';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return array(
            'input_type' => 'datetime',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderSettings()
    {
        $name = 'sonata_type_filter_date';

        if ($this->time) {
            $name .= 'time';
        }

        if ($this->range) {
            $name .= '_range';
        }

        return array(
            $name,
            array(
                'field_type' => $this->getFieldType(),
                'field_options' => $this->getFieldOptions(),
                'label' => $this->getLabel(),
            ),
        );
    }
}
