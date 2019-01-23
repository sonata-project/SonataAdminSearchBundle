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

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\Type\Filter\DefaultType;
use Sonata\CoreBundle\Form\Type\EqualType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

class ClassFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $data)
    {
        if (!$data || !\is_array($data) || !array_key_exists('value', $data)) {
            return;
        }

        if (0 === \strlen($data['value'])) {
            return;
        }

        $data['type'] = !isset($data['type']) ? EqualType::TYPE_IS_EQUAL : $data['type'];

        $operator = $this->getOperator((int) $data['type']);

        if (!$operator) {
            $operator = 'INSTANCE OF';
        }

        $this->applyWhere($queryBuilder, sprintf('%s %s %s', $alias, $operator, $data['value']));
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
    public function getFieldType()
    {
        return $this->getOption('field_type', 'choice');
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldOptions()
    {
        return $this->getOption('choices', [
            'required' => false,
            'choice_list' => new ChoiceList(
                array_values($this->getOption('sub_classes')),
                array_keys($this->getOption('sub_classes'))
            ),
        ]);
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
     * @param int $type
     *
     * @return mixed
     */
    private function getOperator($type)
    {
        $choices = [
            EqualType::TYPE_IS_EQUAL => 'INSTANCE OF',
            EqualType::TYPE_IS_NOT_EQUAL => 'NOT INSTANCE OF',
        ];

        return $choices[$type] ?? false;
    }
}
