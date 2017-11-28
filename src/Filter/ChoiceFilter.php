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

use Elastica\Util;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Sonata\AdminBundle\Form\Type\Filter\DefaultType;

class ChoiceFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $query, $alias, $field, $data)
    {
        if (!$data || !is_array($data) || !array_key_exists('type', $data) || !array_key_exists('value', $data)) {
            return;
        }

        $data['type'] = !isset($data['type']) ? ChoiceType::TYPE_CONTAINS : $data['type'];
        list($firstOperator, $secondOperator) = $this->getOperators((int) $data['type']);

        if (is_array($data['value'])) {
            if (0 == count($data['value'])) {
                return;
            }

            if (in_array('all', $data['value'], true)) {
                return;
            }

            $queryBuilder = new \Elastica\Query\Builder();
            $queryBuilder
            ->fieldOpen($secondOperator)
                ->field($field, Util::escapeTerm($data['value']))
            ->fieldClose();

            if ('must' == $firstOperator) {
                $query->addMust($queryBuilder);
            } else {
                $query->addMustNot($queryBuilder);
            }
        } else {
            if ('' === $data['value'] || null === $data['value'] || false === $data['value'] || 'all' === $data['value']) {
                return;
            }

            $queryBuilder = new \Elastica\Query\Builder();
            $queryBuilder
            ->fieldOpen($secondOperator)
                ->field($field, Util::escapeTerm([$data['value']]))
            ->fieldClose();

            if ('must' == $firstOperator) {
                $query->addMust($queryBuilder);
            } else {
                $query->addMustNot($queryBuilder);
            }
        }
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
        return [DefaultType::class, [
            'operator_type' => 'sonata_type_equal',
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
    private function getOperators($type)
    {
        $choices = [
            ChoiceType::TYPE_CONTAINS => ['must', 'terms'],
            ChoiceType::TYPE_NOT_CONTAINS => ['must_not', 'terms'],
        ];

        return isset($choices[$type]) ? $choices[$type] : false;
    }
}
