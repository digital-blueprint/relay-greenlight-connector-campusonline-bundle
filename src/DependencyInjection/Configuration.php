<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('dbp_relay_greenlight_connector_campusonline');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('secret_token')->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
