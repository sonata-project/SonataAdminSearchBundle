Configuration
-------------

You need to map each admin to a FOS finder service that should be used for that
admin.

.. code-block:: yaml

    sonata_admin_search:
        admin_finder_services:
            my_admin.id: # Admin service id
                finder: Id for a FOS finder service that should be used # Finder service
                actions : [list] #[Optional] actions where elasticsearch has to be enabled
