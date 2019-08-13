<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 24/01/2019
 * Time: 09:18
 */

namespace ScyLabs\NeptuneBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(){
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('neptune')

            ->children()
                ->variableNode('compress')->defaultValue(true)->end()
            ->end()
            ->children()
                ->arrayNode('sitemap')->addDefaultsIfNotSet()
                    ->children()
                        ->variableNode('elements')->defaultValue(false)->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->variableNode('icons')->defaultValue(null)->end()
            ->end()
            ->children()
                ->variableNode('override')->defaultValue(array())->end()
            ->end();

        return $treeBuilder;
    }


}