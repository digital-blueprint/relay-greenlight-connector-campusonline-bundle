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
        return 'greenlight-connector-campusonline';
    }

    private function checkMethod(string $description, callable $func): CheckResult
    {
        $result = new CheckResult($description);
        try {
            $func();
        } catch (\Throwable $e) {
            $result->set(CheckResult::STATUS_FAILURE, $e->getMessage(), ['exception' => $e]);

            return $result;
        }
        $result->set(CheckResult::STATUS_SUCCESS);

        return $result;
    }

    public function check(CheckOptions $options): array
    {
        $results = [];
        $results[] = $this->checkMethod('Check if we can connect to the LDAP server', [$this->ldap, 'checkConnection']);
        $results[] = $this->checkMethod('Check if the LDAP server contains records', [$this->ldap, 'checkHasRecords']);
        $results[] = $this->checkMethod('Check if all configured LDAP attributes exist', [$this->ldap, 'checkMissingAttributes']);
        $results[] = $this->checkMethod('Check if we can connect to the CAMPUSonline API', [$this->co, 'checkConnection']);

        return $results;
    }
}
