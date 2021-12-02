<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service;

use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Entity\Test;

interface TestProviderInterface
{
    public function getTestById(string $identifier): ?Test;

    public function getTests(): array;
}
