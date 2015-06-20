<?php

namespace Sonata\AdminSearchBundle\Tests\Filter;

use Sonata\AdminSearchBundle\Filter\StringFilter;
use Sonata\AdminSearchBundle\ProxyQuery\ElasticaProxyQuery;

class StringFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ElasticaProxyQuery
     */
    protected $proxyQuery;

    public function setup()
    {
        $finder = $this->getMockBuilder('FOS\ElasticaBundle\Finder\TransformedFinder')
            ->disableOriginalConstructor()
            ->getMock();

        $this->proxyQuery = new ElasticaProxyQuery($finder);
    }

    public function testFilterSimple()
    {
        $filter = new StringFilter();
        $value  = 'bar';

        $filter->filter($this->proxyQuery, 'filter', 'foo', array('value' => $value));

        $queryReflection = new \ReflectionClass($this->proxyQuery);
        $queryProperty   = $queryReflection->getProperty('query');

        $queryProperty->setAccessible(true);

        $queryArray = $queryProperty->getValue($this->proxyQuery)->toArray();

        $this->assertEquals($value, $queryArray['query']['bool']['must'][0]['match']['foo']['query']);
    }

    /**
     * Check if filter query with special characters can be translated into JSON.
     */
    public function testFilterSpecialCharacters()
    {
        $filter = new StringFilter();
        $value  = 'bar \ + - && || ! ( ) { } [ ] ^ " ~ * ? : baz';

        $filter->filter($this->proxyQuery, 'filter', 'foo', array('value' => $value));

        $queryReflection = new \ReflectionClass($this->proxyQuery);
        $queryProperty   = $queryReflection->getProperty('query');

        $queryProperty->setAccessible(true);

        $queryArray = $queryProperty->getValue($this->proxyQuery)->toArray();

        $this->assertEquals($value, $queryArray['query']['bool']['must'][0]['match']['foo']['query']);
    }
}
