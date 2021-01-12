# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [1.2.0](https://github.com/sonata-project/SonataAdminBundle/compare/1.0.0...1.1.0) - 2021-01-12
### Added
- Support for ElasticaBundle 5
- Support for Symfony 4

## [1.1.0](https://github.com/sonata-project/SonataAdminBundle/compare/1.0.0...1.1.0) - 2017-11-27
### Changed
 - Changed internal folder structure to `src`, `tests` and `docs`

### Deprecated
- Using `time` and `range` property in subclasses of `Sonata\AdminSearchBundle\Filter\AbstractDateFilter` is deprecated. Please implement `getFilterTypeClass` method which will be an abstract method.

### Removed
- support for old versions of php and Symfony

### Fixed
- Deprecated strings type class names usage.
 
## [1.0.0](https://github.com/sonata-project/SonataAdminBundle/compare/0.1.0...1.0.0) - 2017-01-26
### Fixed
 - `ElasticaDatagridBuilder` now handles autocomplete filter

### Removed
- internal test classes are now excluded from the autoloader
