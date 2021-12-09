<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\DependencyInjection;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class DbpRelayGreenlightConnectorCampusonlineExtension extends ConfigurableExtension
{
    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yaml');

        $ldapCache = $container->register('dbp_api.cache.greenlight_connector_campusonline.ldap', FilesystemAdapter::class);
        $ldapCache->setArguments(['core-ldap', 360, '%kernel.cache_dir%/dbp/greenlight-connector-campusonline-ldap']);
        $ldapCache->setPublic(true);
        $ldapCache->addTag('cache.pool');

        $definition = $container->getDefinition('Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\CampusonlineService');
        $definition->addMethodCall('setConfig', [$mergedConfig['campusonline'] ?? []]);

        $definition = $container->getDefinition('Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\LdapService');
        $definition->addMethodCall('setConfig', [$mergedConfig['ldap'] ?? []]);
        $definition->addMethodCall('setLDAPCache', [$ldapCache, 360]);
    }
}
