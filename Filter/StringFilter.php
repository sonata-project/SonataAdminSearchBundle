<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminSearchBundle\Filter;

use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

class StringFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $query, $alias, $field, $data)
    {
        if (!$data || !is_array($data) || !array_key_exists('value', $data)) {
            return;
        }

        $data['value'] = trim($data['value']);

        if (strlen($data['value']) == 0) {
            return;
        }

        $data['type'] = !isset($data['type']) ?  ChoiceType::TYPE_CONTAINS : $data['type'];

        list($firstOperator, $secondOperator) = $this->getOperators((int) $data['type']);

        // Create a query that match terms (indepedent of terms order) or a phrase
        $queryBuilder = new \Elastica\Query\Builder();
        $queryBuilder
            ->fieldOpen($secondOperator)
                ->fieldOpen($field)
                    ->field('query', str_replace(array('\\', '"'), array('\\\\', '\"'), $data['value']))
                    ->field('operator', 'and')
                ->fieldClose()
            ->fieldClose();

        if ($firstOperator == 'must') {
            $query->addMust($queryBuilder);
        } else {
            $query->addMustNot($queryBuilder);
        }
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    private function getOperators($type)
    {
        $choices = array(
            ChoiceType::TYPE_CONTAINS         => array('must', 'match'),
            ChoiceType::TYPE_NOT_CONTAINS     => array('must_not', 'match'),
            ChoiceType::TYPE_EQUAL            => array('must', 'match_phrase'),
        );

        return isset($choices[$type]) ? $choices[$type] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getRenderSettings()
    {
        return array('sonata_type_filter_choice', array(
            'field_type'    => $this->getFieldType(),
            'field_options' => $this->getFieldOptions(),
            'label'         => $this->getLabel()
        ));
    }
}
