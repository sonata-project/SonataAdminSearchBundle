# SonataAdminSearchBundle

[![Build Status][0]][1]

By default, [SonataAdminBundle][2] uses the storage backend full-text search
capabilities to provide search results or filtered listings. This bundle should
help you leverage the power of your search engine when full-text search is not
good enough.

For the moment, only [elasticsearch][3] is supported, with the help of
[FOSElasticaBundle][4] finder services.

## Installation

    composer require sonata-project/admin-search-bundle

## Configuration

You need to map each admin to a FOS finder service that should be used for that
admin.

```yaml
sonata_admin_search:
    admin_finder_services:
        my_admin.id: # Admin service id
            finder: Id for a FOS finder service that should be used # Finder service
            actions : [list] #[Optional] actions where elasticsearch has to be enabled
```

## Documentation

For contribution to the documentation you cand find it on [Resources/doc](https://github.com/sonata-project/SonataAdminSearchBundle/tree/master/Resources/doc).

[0]:https://travis-ci.org/sonata-project/SonataAdminSearchBundle.svg?branch=master
[1]:https://travis-ci.org/sonata-project/SonataAdminSearchBundle
[2]:http://sonata-project.org/bundles/admin
[3]:http://www.elasticsearch.org/
[4]:https://github.com/FriendsOfSymfony/FOSElasticaBundle
