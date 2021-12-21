<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service;

use Dbp\Relay\CoreBundle\HealthCheck\CheckInterface;
use Dbp\Relay\CoreBundle\HealthCheck\CheckOptions;
use Dbp\Relay\CoreBundle\HealthCheck\CheckResult;

class HealthCheck implements CheckInterface
{
    private $ldap;
    private $co;

    public function __construct(LdapService $ldap, CampusonlineService $co)
    {
        $this->co = $co;
        $this->ldap = $ldap;
    }

    public function getName(): string
    {
        return 'greenlight-campusonline-connector';
    }

    private function checkLDAPConnection(): CheckResult
    {
        $result = new CheckResult('LDAP connection');
        try {
            $this->ldap->checkConnection();
        } catch (\Throwable $e) {
            $result->set(CheckResult::STATUS_FAILURE, $e->getMessage());

            return $result;
        }
        $result->set(CheckResult::STATUS_SUCCESS);

        return $result;
    }

    private function checkCOConnection(): CheckResult
    {
        $result = new CheckResult('CO connection');
        try {
            $this->co->checkConnection();
        } catch (\Throwable $e) {
            $result->set(CheckResult::STATUS_FAILURE, $e->getMessage());

            return $result;
        }
        $result->set(CheckResult::STATUS_SUCCESS);

        return $result;
    }

    public function check(CheckOptions $options): array
    {
        $results = [];
        $results[] = $this->checkLDAPConnection();
        $results[] = $this->checkCOConnection();

        return $results;
    }
}
