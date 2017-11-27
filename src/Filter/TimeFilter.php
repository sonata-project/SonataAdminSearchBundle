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

/**
 * NEXT_MAJOR: Remove this class, this is same with DateTimeFilter class.
 *
 * @deprecated since 1.1, will be removed in 2.0. Use `Sonata\AdminSearchBundle\Filter\DateTimeFilter' instead.
 */
class TimeFilter extends AbstractDateFilter
{
    /**
     * This filter does not allow filtering by time.
     *
     * @var bool
     */
    protected $time = true;
}
