# SonataAdminSearchBundle

Provides search engine integration with [SonataAdminBundle][0].

[0]:http://sonata-project.org/bundles/admin

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
```
