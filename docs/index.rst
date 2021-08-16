Sonata Admin Search Bundle
==========================

By default, SonataAdminBundle_ uses the storage backend full-text search
capabilities to provide search results or filtered listings. This bundle should
help you leverage the power of your search engine when full-text search is not
good enough.

For the moment, only elasticsearch_ is supported, with the help of
FOSElasticaBundle_ finder services.

Warning: don't use this bundle if you're not willing to do what it takes to fix
it when it breaks.
There is no main developer for it at the moment,
but we do review and merge PRs pretty quickly.

.. _SonataAdminBundle: https://docs.sonata-project.org/projects/SonataAdminBundle/en/3.x/
.. _elasticsearch: http://www.elasticsearch.org/
.. _FOSElasticaBundle: https://github.com/FriendsOfSymfony/FOSElasticaBundle

Reference Guide
---------------

.. toctree::
   :maxdepth: 1
   :numbered:

   reference/installation
   reference/configuration
   reference/createquery
   reference/filter_field_definition
