<?php

namespace Sonata\AdminSearchBundle\Tests\ProxyQuery;

use FOS\ElasticaBundle\Finder\TransformedFinder;
use Sonata\AdminSearchBundle\ProxyQuery\ElasticaProxyQuery;

class ElasticaProxyQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TransformedFinder
     */
    protected $finder;

    /**
     * @var ElasticaProxyQuery
     */
    protected $proxyQuery;

    protected $fieldMapping = array(
        'fieldName'  => 'name',
        'type'       => 'string',
        'columnName' => 'name',
    );

    public function setup()
    {
        $this->finder = $this->getMockBuilder('FOS\ElasticaBundle\Finder\TransformedFinder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->proxyQuery = new ElasticaProxyQuery($this->finder);
    }

    public function testSortByNoParent()
    {
        $this->proxyQuery->setSortBy(null, $this->fieldMapping);

        $this->assertEquals('name', $this->proxyQuery->getSortBy());
    }

    public function testSortByWithParent()
    {
        $parentMapping = array(
            array(
                'fieldName' => 'category'
            )
        );

        $this->proxyQuery->setSortBy($parentMapping, $this->fieldMapping);

        $this->assertEquals('category.name', $this->proxyQuery->getSortBy());
    }

    public function testSortOrder()
    {
        $this->proxyQuery->setSortOrder('ASC');

        $this->assertEquals('ASC', $this->proxyQuery->getSortOrder());
    }

    /**
     * Test if "setSort" method of Elastica query has been called
     */
    public function testExecuteWithSort()
    {
        $this->finder->expects($this->once())
            ->method('createPaginatorAdapter');

        $this->proxyQuery
            ->setSortBy(null, $this->fieldMapping)
            ->setSortOrder('DESC');

        $this->proxyQuery->execute();

        $queryReflection = new \ReflectionClass($this->proxyQuery);
        $queryProperty   = $queryReflection->getProperty('query');

        $queryProperty->setAccessible(true);

        $this->assertEquals(
            array('name' => array('order' => 'desc')),
            $queryProperty->getValue($this->proxyQuery)->getParam('sort')
        );
    }
}