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
use Sonata\AdminBundle\Form\Type\Filter\NumberType;
use Sonata\AdminBundle\Form\Type\Operator\NumberOperatorType;

class NumberFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $query, $alias, $field, $data)
    {
        if (!$data || !\is_array($data) || !\array_key_exists('value', $data) || !is_numeric($data['value'])) {
            return;
        }

        $type = $data['type'] ?? false;
        $operator = $this->getOperator($type);

        $queryBuilder = new QueryBuilder();

        if (false === $operator) {
            // Match query to get equality
            $innerQuery = $queryBuilder
                ->query()
                ->match($field, $data['value']);
        } else {
            // Range query
            $innerQuery = $queryBuilder
                ->query()
                ->range($field, [
                    $operator => $data['value'],
                ]);
        }

        $query->addMust($innerQuery);
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
            NumberOperatorType::TYPE_EQUAL => false,
            NumberOperatorType::TYPE_GREATER_EQUAL => 'gte',
            NumberOperatorType::TYPE_GREATER_THAN => 'gt',
            NumberOperatorType::TYPE_LESS_EQUAL => 'lte',
            NumberOperatorType::TYPE_LESS_THAN => 'lt',
        ];

        return $choices[$type] ?? false;
    }
}
