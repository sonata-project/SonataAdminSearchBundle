<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminSearchBundle\Datagrid;

use Sonata\AdminBundle\Datagrid\Pager as BasePager;

class Pager extends BasePager
{
    private $paginator;

    /**
     * {@inheritdoc}
     */
    public function computeNbResult()
    {
        return $this->getPaginator()->getTotalHits();
    }

    /**
     * {@inheritdoc}
     */
    public function getResults()
    {
        return $this->getPaginator()->getResults(
            $this->getQuery()->getFirstResult(),
            $this->getQuery()->getMaxResults()
        )->toArray();
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
            $this->setNbResults($this->computeNbResult());
        } else {
            $offset = ($this->getPage() - 1) * $this->getMaxPerPage();

            $this->getQuery()->setFirstResult($offset);
            $this->getQuery()->setMaxResults($this->getMaxPerPage());
            $this->setNbResults($this->computeNbResult());
            $this->setLastPage(ceil($this->getNbResults() / $this->getMaxPerPage()));
        }
    }

    protected function getPaginator()
    {
        if (null === $this->paginator) {
            $this->paginator = $this->getQuery()->execute();
        }

        return $this->paginator;
    }
}
