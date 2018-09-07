<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminSearchBundle\Filter;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\Type\Filter\NumberType;

class NumberFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $query, $alias, $field, $data)
    {
        if (!$data || !\is_array($data) || !array_key_exists('value', $data) || !is_numeric($data['value'])) {
            return;
        }

        $type = isset($data['type']) ? $data['type'] : false;
        $operator = $this->getOperator($type);

        $queryBuilder = new \Elastica\Query\Builder();

        if (false === $operator) {
            // Match query to get equality
            $queryBuilder
                ->fieldOpen('match')
                    ->field($field, $data['value'])
                ->fieldClose();
        } else {
            // Range query
            $queryBuilder
                ->range()
                    ->fieldOpen($field)
                        ->field($operator, $data['value'])
                    ->fieldClose()
                ->rangeClose();
        }

        $query->addMust($queryBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderSettings()
    {
        return [NumberType::class, [
            'field_type' => $this->getFieldType(),
            'field_options' => $this->getFieldOptions(),
            'label' => $this->getLabel(),
        ]];
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function getOperator($type)
    {
        $choices = [
            NumberType::TYPE_EQUAL => false,
            NumberType::TYPE_GREATER_EQUAL => 'gte',
            NumberType::TYPE_GREATER_THAN => 'gt',
            NumberType::TYPE_LESS_EQUAL => 'lte',
            NumberType::TYPE_LESS_THAN => 'lt',
        ];

        return isset($choices[$type]) ? $choices[$type] : false;
    }
}
