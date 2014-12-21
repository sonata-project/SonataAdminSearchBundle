<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminSearchBundle\ProxyQuery;

use Elastica\Search;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Elastica\Query\AbstractQuery;
use Elastica\Query;

class ElasticaProxyQuery extends Query implements ProxyQueryInterface
{
    private $finder;

    public function __construct(
        $queryBuilder,
        TransformedFinder $finder
    ) {
        $this->queryBuilder  = $queryBuilder;
        $this->finder = $finder;
    }

    public function getQuery()
    {
        return new Query($this->queryBuilder->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $params = array(), $hydrationMode = null)
    {
        // TODO find method names

        /* // Sorted field and sort order */
        /* $sortBy = $this->getSortBy(); */
        /* $sortOrder = $this->getSortOrder(); */

        /* if ($sortBy && $sortOrder) { */
        /*     $query->setSort(array($sortBy => array('order' => $sortOrder))); */
        /* } */

        /* // Limit & offset */
        $this->queryBuilder
            ->from($this->getFirstResult())
            ->size($this->getMaxResults());
        /* $this->results = $this->queryBuilder->getRepository()->createPaginatorAdapter($query, array( */
        /*     Search::OPTION_SIZE => $this->getMaxResults(), */
        /*     Search::OPTION_FROM => $this->getFirstResult(), */
        /* )); */

        return $this->finder->findPaginated($this->getQuery());
        /* return $this->results->getResults($this->getFirstResult(), $this->getMaxResults())->toArray(); */
    }

    /**
     * @var array
     */
    protected $sortBy;

    /**
     * @var array
     */
    protected $sortOrder;

    /**
     * @var integer
     */
    protected $firstResult;

    /**
     * @var integer
     */
    protected $maxResults;

    /**
     * @var array
     */
    protected $results;


    /**
     * {@inheritdoc}
     */
    public function setSortBy($parentAssociationMappings, $fieldMapping)
    {
        // TODO
    }

    /**
     * {@inheritdoc}
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstResult($firstResult)
    {
        $this->firstResult = $firstResult;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstResult()
    {
        return $this->firstResult;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * @return mixed
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getResults()
    {
        return $this->results;
    }

    public function getSingleScalarResult()
    {
        // TODO
    }

    public function getUniqueParameterId()
    {
        // TODO
    }

    public function entityJoin(array $associationMappings)
    {
        // TODO
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        $this->queryBuilder = clone $this->queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function __call($name, $args)
    {
        return call_user_func_array(array($this->queryBuilder, $name), $args);
    }
}
