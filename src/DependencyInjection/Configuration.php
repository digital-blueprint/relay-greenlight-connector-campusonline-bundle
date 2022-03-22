<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('dataservice_override');
        $dsNode = $treeBuilder->getRootNode()
            ->info('An optional mapping of dataservice IDs to their replacements')
            ->arrayPrototype()
                ->children()
                    ->scalarNode('name')
                        ->info('The name of the dataservice to override')
                        ->example('brm.pm.extension.ucardfoto')
                        ->isRequired()
                    ->end()
                    ->scalarNode('replacement')
                        ->info('The replacement dataservice')
                        ->example('loc_locinucfotods.ucardfoto')
                        ->isRequired()
                    ->end()
                ->end()
            ->end();

        $coBuilder = new TreeBuilder('campusonline');
        $coNode = $coBuilder->getRootNode()
            ->children()
                ->scalarNode('api_url')
                    ->isRequired()
                    ->info('The base URL of the CO instance')
                    ->example('https://online.mycampus.org/campus_online')
                ->end()
                ->scalarNode('client_id')
                    ->isRequired()
                    ->info('The OAuth2 client ID')
                    ->example('my-client')
                ->end()
                ->scalarNode('client_secret')
                    ->isRequired()
                    ->info('The OAuth2 client secret')
                    ->example('my-secret')
                ->end()
            ->end()
            ->append($dsNode);

        $treeBuilder = new TreeBuilder('co_identifier_attributes');
        $coAttrNode = $treeBuilder->getRootNode()
            ->info('LDAP attribute names that correspond to IDs in CAMPUSonline. At least one of the attributes needs to be set')
            ->children()
                ->scalarNode('ident_nr_obfuscated')
                    ->info('The LDAP attribute name for IDENT_NR_OBFUSCATED')
                ->end()
                ->scalarNode('ident_nr')
                    ->info('The LDAP attribute name for IDENT_NR')
                ->end()
                ->scalarNode('person_nr')
                    ->info('The LDAP attribute name for PERSON_NR')
                ->end()
            ->end();

        $ldapBuilder = new TreeBuilder('ldap');
        $ldapNode = $ldapBuilder->getRootNode()
            ->children()
                ->scalarNode('host')
                    ->isRequired()
                ->end()
                ->scalarNode('base_dn')
                    ->isRequired()
                ->end()
                ->scalarNode('username')
                    ->isRequired()
                ->end()
                ->scalarNode('password')
                    ->isRequired()
                ->end()
                ->enumNode('encryption')
                    ->info('simple_tls uses port 636 and is sometimes referred to as "SSL", start_tls uses port 389 and is sometimes referred to as "TLS"')
                    ->values(['start_tls', 'simple_tls'])
                    ->defaultValue('start_tls')
                ->end()
                ->scalarNode('identifier_attribute')
                    ->isRequired()
                ->end()
                ->append($coAttrNode)
            ->end();

        $treeBuilder = new TreeBuilder('dbp_relay_greenlight_connector_campusonline');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->append($coNode);
        $rootNode->append($ldapNode);

        return $treeBuilder;
    }
}
