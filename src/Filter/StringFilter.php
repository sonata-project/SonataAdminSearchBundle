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

use Elastica\Query\MatchPhrase;
use Elastica\QueryBuilder;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Sonata\AdminBundle\Form\Type\Operator\ContainsOperatorType;

class StringFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $query, $alias, $field, $data)
    {
        if (!$data || !\is_array($data) || !\array_key_exists('value', $data)) {
            return;
        }

        $data['value'] = trim($data['value']);

        if ('' === $data['value']) {
            return;
        }

        $data['type'] = !isset($data['type']) ? ContainsOperatorType::TYPE_CONTAINS : $data['type'];

        [$firstOperator, $secondOperator] = $this->getOperators((int) $data['type']);

        // Create a query that match terms (indepedent of terms order) or a phrase
        $queryBuilder = new QueryBuilder();

        if ('match_phrase' === $secondOperator) {
            $innerQuery = new MatchPhrase($field, [
                'query' => str_replace(['\\', '"'], ['\\\\', '\"'], $data['value']),
                'operator' => 'and',
            ]);
        } else {
            $innerQuery = $queryBuilder
                ->query()
                ->match($field, [
                    'query' => str_replace(['\\', '"'], ['\\\\', '\"'], $data['value']),
                    'operator' => 'and',
                ]);
        }

        if ('must' === $firstOperator) {
            $query->addMust($innerQuery);
        } else {
            $query->addMustNot($innerQuery);
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
        return [ChoiceType::class, [
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
            ContainsOperatorType::TYPE_CONTAINS => ['must', 'match'],
            ContainsOperatorType::TYPE_NOT_CONTAINS => ['must_not', 'match'],
            ContainsOperatorType::TYPE_EQUAL => ['must', 'match_phrase'],
        ];

        return $choices[$type] ?? false;
    }
}
