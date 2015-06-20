<?php

namespace Sonata\AdminSearchBundle\Tests\Filter;

use Sonata\AdminSearchBundle\Filter\BooleanFilter;
use Sonata\AdminSearchBundle\ProxyQuery\ElasticaProxyQuery;
use Sonata\CoreBundle\Form\Type\BooleanType;

class BooleanFilterTest extends \PHPUnit_Framework_TestCase
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

    public function testNoFilterSimple()
    {
        $filter = new BooleanFilter();
        $value  = BooleanType::TYPE_NO;

        $filter->filter($this->proxyQuery, 'filter', 'foo', array('value' => $value, 'type' => null));

        $queryReflection = new \ReflectionClass($this->proxyQuery);
        $queryProperty   = $queryReflection->getProperty('query');

        $queryProperty->setAccessible(true);

        $queryArray = $queryProperty->getValue($this->proxyQuery)->toArray();

        $this->assertEquals('false', $queryArray['query']['bool']['must'][0]['term']['foo']);
    }

    public function testYesFilterSimple()
    {
        $filter = new BooleanFilter();
        $value  = BooleanType::TYPE_YES;

        $filter->filter($this->proxyQuery, 'filter', 'foo', array('value' => $value, 'type' => null));

        $queryReflection = new \ReflectionClass($this->proxyQuery);
        $queryProperty   = $queryReflection->getProperty('query');

        $queryProperty->setAccessible(true);

        $queryArray = $queryProperty->getValue($this->proxyQuery)->toArray();

        $this->assertEquals('true', $queryArray['query']['bool']['must'][0]['term']['foo']);
    }
}
