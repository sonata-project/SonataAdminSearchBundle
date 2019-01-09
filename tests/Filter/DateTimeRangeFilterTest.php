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

namespace Sonata\AdminSearchBundle\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Sonata\AdminBundle\Form\Type\Filter\DateTimeRangeType;
use Sonata\AdminSearchBundle\Filter\DateTimeRangeFilter;

/**
 * @author Ahmet Akbana <ahmetakbana@gmail.com>
 */
class DateTimeRangeFilterTest extends TestCase
{
    public function testGetFilterTypeClass()
    {
        $filter = new DateTimeRangeFilter();

        $renderSettings = $filter->getRenderSettings();

        $this->assertSame(DateTimeRangeType::class, $renderSettings[0]);
    }
}
