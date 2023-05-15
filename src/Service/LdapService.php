<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service;

use Adldap\Adldap;
use Adldap\AdldapException;
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

    private $coPersonNrAttributeName;

    private $coIdentNrAttributeName;

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
        ];

        $encryption = $config['encryption'];
        assert(in_array($encryption, ['start_tls', 'simple_tls'], true));
        $this->providerConfig['use_tls'] = ($encryption === 'start_tls');
        $this->providerConfig['use_ssl'] = ($encryption === 'simple_tls');
        $this->providerConfig['port'] = ($encryption === 'start_tls') ? 389 : 636;

        $this->identifierAttributeName = $config['identifier_attribute'] ?? null;
        if ($this->identifierAttributeName === '') {
            $this->identifierAttributeName = null;
        }

        $coAttributes = $config['co_identifier_attributes'];
        $this->coIdentNrObfuscatedAttributeName = $coAttributes['ident_nr_obfuscated'] ?? null;
        if ($this->coIdentNrObfuscatedAttributeName === '') {
            $this->coIdentNrObfuscatedAttributeName = null;
        }
        $this->coIdentNrAttributeName = $coAttributes['ident_nr'] ?? null;
        if ($this->coIdentNrAttributeName === '') {
            $this->coIdentNrAttributeName = null;
        }
        $this->coPersonNrAttributeName = $coAttributes['person_nr'] ?? null;
        if ($this->coPersonNrAttributeName === '') {
            $this->coPersonNrAttributeName = null;
        }
    }

    public function checkConnection()
    {
        $provider = $this->getProvider();
        $builder = $this->getCachedBuilder($provider);
        $builder->first();
    }

    public function checkHasRecords()
    {
        $provider = $this->getProvider();
        $builder = $this->getCachedBuilder($provider);
        $user = $builder->where('objectClass', '=', $provider->getSchema()->person())->first();
        if ($user === null) {
            throw new \RuntimeException('LDAP has no records');
        }
    }

    public function checkAttributeExists(string $attribute): bool
    {
        $provider = $this->getProvider();
        $builder = $this->getCachedBuilder($provider);

        /** @var User $user */
        $user = $builder
            ->where('objectClass', '=', $provider->getSchema()->person())
            ->whereHas($attribute)
            ->first();

        return $user !== null;
    }

    public function checkMissingAttributes()
    {
        $attributes = [
            $this->identifierAttributeName,
            $this->coIdentNrObfuscatedAttributeName,
            $this->coIdentNrAttributeName,
            $this->coPersonNrAttributeName,
        ];

        $missing = [];
        foreach ($attributes as $attr) {
            if ($attr !== null && !$this->checkAttributeExists($attr)) {
                $missing[] = $attr;
            }
        }

        if (count($missing) > 0) {
            throw new \RuntimeException('The following LDAP attributes were not found: '.join(', ', $missing));
        }
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

    public function getCoIdent(string $identifier): CoIdent
    {
        if ($this->identifierAttributeName === null) {
            throw new \RuntimeException('identifier attribute not configured');
        }

        try {
            $provider = $this->getProvider();
            $builder = $this->getCachedBuilder($provider);

            /** @var User $user */
            $user = $builder
                ->where('objectClass', '=', $provider->getSchema()->person())
                ->whereEquals($this->identifierAttributeName, $identifier)
                ->first();
        } catch (AdldapException $e) {
            // There was an issue binding / connecting to the server.
            throw new ApiError(Response::HTTP_BAD_GATEWAY, sprintf("Person with id '%s' could not be loaded! Message: %s", $identifier, CoreTools::filterErrorMessage($e->getMessage())));
        }

        if ($user === null) {
            throw new NotFoundHttpException(sprintf("Person with id '%s' could not be found!", $identifier));
        }

        $ident = new CoIdent();
        if ($this->coIdentNrObfuscatedAttributeName !== null) {
            $ident->identIdObfuscated = $user->getFirstAttribute($this->coIdentNrObfuscatedAttributeName);
        }
        if ($this->coIdentNrAttributeName !== null) {
            $ident->identId = $user->getFirstAttribute($this->coIdentNrAttributeName);
        }
        if ($this->coPersonNrAttributeName !== null) {
            $ident->personId = $user->getFirstAttribute($this->coPersonNrAttributeName);
        }

        return $ident;
    }

    public static function getSubscribedServices(): array
    {
        return [
            UserSessionInterface::class,
        ];
    }
}
