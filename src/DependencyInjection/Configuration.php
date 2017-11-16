<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\AdminSearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sonata_admin_search');

        $rootNode
            ->children()
                ->arrayNode('admin_finder_services')
                  ->useAttributeAsKey('admin_id')
                  ->prototype('array')
                      ->addDefaultsIfNotSet()
                      ->children()
                          ->scalarNode('finder')->isRequired()->end()
                          ->arrayNode('actions')
                              ->defaultValue(['list', 'retrieveAutocompleteItems'])
                              ->prototype('scalar')
                              ->info('array of actions where elasticsearch has to be enabled')
                           ->end()
                      ->end()
                  ->end()
            ->end();

        return $treeBuilder;
    }
}
