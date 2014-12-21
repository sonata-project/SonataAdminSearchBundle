<?php

namespace Sonata\AdminSearchBundle\Datagrid;

use Sonata\AdminBundle\Datagrid\Pager as BasePager;

class Pager extends BasePager
{
    private $paginatedResults;

    protected function findPaginated()
    {
        if (is_null($this->paginatedResults)) {
            $this->paginatedResults = $this->getQuery()->execute();
        }

        return $this->paginatedResults;
    }


    /**
     * {@inheritdoc}
     */
    public function computeNbResult()
    {
        return $this->findPaginated()->getNbResults();
    }

    /**
     * {@inheritdoc}
     */
    public function getResults()
    {
        return $this->findPaginated();
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->resetIterator();


        $this->getQuery()->setFirstResult(null);
        $this->getQuery()->setMaxResults(null);

        if (count($this->getParameters()) > 0) {
            $this->getQuery()->setParameters($this->getParameters());
        }

        if (0 == $this->getPage() || 0 == $this->getMaxPerPage()) {
            $this->setLastPage(0);
        } else {
            $offset = ($this->getPage() - 1) * $this->getMaxPerPage();

            $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));

            $this->getQuery()->setFirstResult($offset);
            $this->getQuery()->setMaxResults($this->getMaxPerPage());
        }
        $this->setNbResults($this->computeNbResult());
    }
}
