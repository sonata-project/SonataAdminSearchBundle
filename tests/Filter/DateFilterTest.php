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
use Sonata\AdminBundle\Form\Type\Filter\DateType;
use Sonata\AdminSearchBundle\Filter\DateFilter;

/**
 * @author Ahmet Akbana <ahmetakbana@gmail.com>
 */
class DateFilterTest extends TestCase
{
    public function testGetFilterTypeClass(): void
    {
        $filter = new DateFilter();

        $renderSettings = $filter->getRenderSettings();

        $this->assertSame(DateType::class, $renderSettings[0]);
    }
}
