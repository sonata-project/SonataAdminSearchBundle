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

namespace Sonata\AdminSearchBundle\Tests\ProxyQuery;

use FOS\ElasticaBundle\Finder\TransformedFinder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sonata\AdminSearchBundle\ProxyQuery\ElasticaProxyQuery;

class ElasticaProxyQueryTest extends TestCase
{
    /**
     * @var MockObject|TransformedFinder
     */
    protected $finder;

    /**
     * @var ElasticaProxyQuery
     */
    protected $proxyQuery;

    protected $fieldMapping = [
        'fieldName' => 'name',
        'type' => 'string',
        'columnName' => 'name',
    ];

    protected function setUp(): void
    {
        $this->finder = $this->createMock(TransformedFinder::class);

        $this->proxyQuery = new ElasticaProxyQuery($this->finder);
    }

    public function testSortByNoParent(): void
    {
        $this->proxyQuery->setSortBy(null, $this->fieldMapping);

        static::assertSame('name', $this->proxyQuery->getSortBy());
    }

    public function testSortByWithParent(): void
    {
        $parentMapping = [
            [
                'fieldName' => 'category',
            ],
        ];

        $this->proxyQuery->setSortBy($parentMapping, $this->fieldMapping);

        static::assertSame('category.name', $this->proxyQuery->getSortBy());
    }

    public function testSortOrder(): void
    {
        $this->proxyQuery->setSortOrder('ASC');

        static::assertSame('ASC', $this->proxyQuery->getSortOrder());
    }

    /**
     * Test if "setSort" method of Elastica query has been called.
     */
    public function testExecuteWithSort(): void
    {
        $this->finder->expects(static::once())
            ->method('createPaginatorAdapter');

        $this->proxyQuery
            ->setSortBy(null, $this->fieldMapping)
            ->setSortOrder('DESC');

        $this->proxyQuery->execute();

        $queryReflection = new \ReflectionClass($this->proxyQuery);
        $queryProperty = $queryReflection->getProperty('query');

        $queryProperty->setAccessible(true);

        static::assertSame(
            ['name' => ['order' => 'desc']],
            $queryProperty->getValue($this->proxyQuery)->getParam('sort')
        );
    }
}
