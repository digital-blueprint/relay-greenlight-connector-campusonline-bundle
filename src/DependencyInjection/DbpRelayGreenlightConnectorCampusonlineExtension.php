<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\DependencyInjection;

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

        $definition = $container->getDefinition('Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\CampusonlineService');
        $definition->addMethodCall('setConfig', [$mergedConfig['campusonline'] ?? []]);

        $definition = $container->getDefinition('Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\LdapService');
        $definition->addMethodCall('setConfig', [$mergedConfig['ldap'] ?? []]);
    }
}
