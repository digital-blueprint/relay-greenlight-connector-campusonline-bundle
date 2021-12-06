<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $coBuilder = new TreeBuilder('campusonline');
        $coNode = $coBuilder->getRootNode()
            ->children()
                ->scalarNode('api_url')
                    ->info('The base URL of the CO instance')
                    ->example('https://online.mycampus.org/campus_online')
                ->end()
                ->scalarNode('client_id')
                    ->info('The OAuth2 client ID')
                    ->example('my-client')
                ->end()
                ->scalarNode('client_secret')
                    ->info('The OAuth2 client secret')
                    ->example('my-secret')
                ->end()
                ->scalarNode('dataservice')
                    ->info('The dataservice name of the ucardfoto service in case the default isn\'t used')
                    ->defaultValue('brm.pm.extension.ucardfoto')
                ->end()
            ->end();

        $ldapBuilder = new TreeBuilder('ldap');
        $ldapNode = $ldapBuilder->getRootNode()
            ->children()
                ->scalarNode('host')
                ->end()
                ->scalarNode('base_dn')
                ->end()
                ->scalarNode('username')
                ->end()
                ->scalarNode('password')
                ->end()
                ->scalarNode('identifier_attribute')
                ->end()
                ->scalarNode('co_ident_nr_obfuscated_attribute')
                ->end()
            ->end();

        $treeBuilder = new TreeBuilder('dbp_relay_greenlight_connector_campusonline');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->append($coNode);
        $rootNode->append($ldapNode);

        return $treeBuilder;
    }
}
