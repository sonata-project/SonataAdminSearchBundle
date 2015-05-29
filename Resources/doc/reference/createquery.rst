Customizing the query used to generate the list
===============================================


You can customize the list query thanks to the ``createQuery`` method.

.. code-block:: php

    <?php

    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);

        $queryparam = new \Elastica\Query\Match();
        $queryparam->setFieldQuery('my_param', 'my_value');
        $query->addMust($queryparam);

        return $query;
    }

