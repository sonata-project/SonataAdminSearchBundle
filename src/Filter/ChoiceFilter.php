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
use Elastica\Util;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\Type\Filter\DefaultType;
use Sonata\AdminBundle\Form\Type\Operator\ContainsOperatorType;
use Sonata\Form\Type\EqualType;

class ChoiceFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $query, $alias, $field, $data)
    {
        if (!$data || !\is_array($data) || !\array_key_exists('type', $data) || !\array_key_exists('value', $data)) {
            return;
        }

        $data['type'] = !isset($data['type']) ? ContainsOperatorType::TYPE_CONTAINS : $data['type'];
        [$firstOperator, $secondOperator] = $this->getOperators((int) $data['type']);

        if (\is_array($data['value'])) {
            if (0 === \count($data['value'])) {
                return;
            }

            if (\in_array('all', $data['value'], true)) {
                return;
            }

            $queryBuilder = new QueryBuilder();
            $innerQuery = $queryBuilder
                ->query()
                ->terms([$field => Util::escapeTerm($data['value'])]);

            if ('must' === $firstOperator) {
                $query->addMust($innerQuery);
            } else {
                $query->addMustNot($innerQuery);
            }
        } else {
            if ('' === $data['value'] || null === $data['value'] || false === $data['value'] || 'all' === $data['value']) {
                return;
            }

            $queryBuilder = new QueryBuilder();
            $innerQuery = $queryBuilder
                ->query()
                ->terms([$field => Util::escapeTerm($data['value'])]);

            if ('must' === $firstOperator) {
                $query->addMust($innerQuery);
            } else {
                $query->addMustNot($innerQuery);
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
            'operator_type' => EqualType::class,
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
            ContainsOperatorType::TYPE_CONTAINS => ['must', 'terms'],
            ContainsOperatorType::TYPE_NOT_CONTAINS => ['must_not', 'terms'],
        ];

        return $choices[$type] ?? false;
    }
}
