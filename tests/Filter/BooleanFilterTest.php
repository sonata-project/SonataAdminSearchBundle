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

use FOS\ElasticaBundle\Finder\TransformedFinder;
use PHPUnit\Framework\TestCase;
use Sonata\AdminSearchBundle\Filter\BooleanFilter;
use Sonata\AdminSearchBundle\ProxyQuery\ElasticaProxyQuery;
use Sonata\Form\Type\BooleanType;

class BooleanFilterTest extends TestCase
{
    /**
     * @var ElasticaProxyQuery
     */
    protected $proxyQuery;

    protected function setUp(): void
    {
        $finder = $this->createMock(TransformedFinder::class);

        $this->proxyQuery = new ElasticaProxyQuery($finder);
    }

    public function testNoFilterSimple(): void
    {
        $filter = new BooleanFilter();
        $value = BooleanType::TYPE_NO;

        $filter->filter($this->proxyQuery, 'filter', 'foo', ['value' => $value, 'type' => null]);

        $queryReflection = new \ReflectionClass($this->proxyQuery);
        $queryProperty = $queryReflection->getProperty('query');

        $queryProperty->setAccessible(true);

        $queryArray = $queryProperty->getValue($this->proxyQuery)->toArray();

        static::assertFalse($queryArray['query']['bool']['must'][0]['term']['foo']);
    }

    public function testYesFilterSimple(): void
    {
        $filter = new BooleanFilter();
        $value = BooleanType::TYPE_YES;

        $filter->filter($this->proxyQuery, 'filter', 'foo', ['value' => $value, 'type' => null]);

        $queryReflection = new \ReflectionClass($this->proxyQuery);
        $queryProperty = $queryReflection->getProperty('query');

        $queryProperty->setAccessible(true);

        $queryArray = $queryProperty->getValue($this->proxyQuery)->toArray();

        static::assertTrue($queryArray['query']['bool']['must'][0]['term']['foo']);
    }
}
