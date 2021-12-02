<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service;

use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Entity\Test;

class ExternalApi implements TestProviderInterface
{
    private $tests;

    public function __construct(MyCustomService $service)
    {
        // Make phpstan happy
        $service = $service;

        $this->tests = [];
        $test1 = new Test();
        $test1->setIdentifier('graz');
        $test1->setName('Graz');

        $test2 = new Test();
        $test2->setIdentifier('vienna');
        $test2->setName('Vienna');

        $this->tests[] = $test1;
        $this->tests[] = $test2;
    }

    public function getTestById(string $identifier): ?Test
    {
        foreach ($this->tests as $test) {
            if ($test->getIdentifier() === $identifier) {
                return $test;
            }
        }

        return null;
    }

    public function getTests(): array
    {
        return $this->tests;
    }
}
