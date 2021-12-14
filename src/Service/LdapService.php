<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service;

use Adldap\Adldap;
use Adldap\Connections\Provider;
use Adldap\Connections\ProviderInterface;
use Adldap\Models\User;
use Adldap\Query\Builder;
use Dbp\Relay\CoreBundle\API\UserSessionInterface;
use Dbp\Relay\CoreBundle\Exception\ApiError;
use Dbp\Relay\CoreBundle\Helpers\Tools as CoreTools;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class LdapService implements LoggerAwareInterface, ServiceSubscriberInterface
{
    use LoggerAwareTrait;

    /**
     * @var Adldap
     */
    private $ad;

    private $cachePool;

    private $cacheTTL;

    private $providerConfig;

    private $identifierAttributeName;

    private $coIdentNrObfuscatedAttributeName;

    public function __construct()
    {
        $this->ad = new Adldap();
        $this->cacheTTL = 0;
    }

    public function setConfig(array $config)
    {
        $this->providerConfig = [
            'hosts' => [$config['host'] ?? ''],
            'base_dn' => $config['base_dn'] ?? '',
            'username' => $config['username'] ?? '',
            'password' => $config['password'] ?? '',
            'use_tls' => true,
        ];

        $this->identifierAttributeName = $config['identifier_attribute'] ?? '';
        $this->coIdentNrObfuscatedAttributeName = $config['co_ident_nr_obfuscated_attribute'] ?? '';
    }

    public function checkConnection()
    {
        $provider = $this->getProvider();
        $builder = $this->getCachedBuilder($provider);
        $builder->first();
    }

    public function setLDAPCache(?CacheItemPoolInterface $cachePool, int $ttl)
    {
        $this->cachePool = $cachePool;
        $this->cacheTTL = $ttl;
    }

    private function getProvider(): ProviderInterface
    {
        if ($this->logger !== null) {
            Adldap::setLogger($this->logger);
        }
        $ad = new Adldap();
        $ad->addProvider($this->providerConfig);
        $provider = $ad->connect();
        assert($provider instanceof Provider);
        if ($this->cachePool !== null) {
            $provider->setCache(new Psr16Cache($this->cachePool));
        }

        return $provider;
    }

    private function getCachedBuilder(ProviderInterface $provider): Builder
    {
        // FIXME: https://github.com/Adldap2/Adldap2/issues/786
        // return $provider->search()->cache($until=$this->cacheTTL);
        // We depend on the default TTL of the cache for now...

        /**
         * @var Builder $builder
         */
        $builder = $provider->search()->cache();

        return $builder;
    }

    public function getCoIdentNrObfuscated(string $identifier): string
    {
        try {
            $provider = $this->getProvider();
            $builder = $this->getCachedBuilder($provider);

            /** @var User $user */
            $user = $builder
                ->where('objectClass', '=', $provider->getSchema()->person())
                ->whereEquals($this->identifierAttributeName, $identifier)
                ->first();

            if ($user === null) {
                throw new NotFoundHttpException(sprintf("Person with id '%s' could not be found!", $identifier));
            }

            return $user->getFirstAttribute($this->coIdentNrObfuscatedAttributeName);
        } catch (\Adldap\Auth\BindException $e) {
            // There was an issue binding / connecting to the server.
            throw new ApiError(Response::HTTP_BAD_GATEWAY, sprintf("Person with id '%s' could not be loaded! Message: %s", $identifier, CoreTools::filterErrorMessage($e->getMessage())));
        }
    }

    public static function getSubscribedServices()
    {
        return [
            UserSessionInterface::class,
        ];
    }
}
