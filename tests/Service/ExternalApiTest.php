<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Tests\Service;

use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\MyCustomService;
use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\ExternalApi;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ExternalApiTest extends WebTestCase
{
    private $api;

    protected function setUp(): void
    {
        $service = new MyCustomService('secret-test-custom');
        $this->api = new ExternalApi($service);
    }

    public function test()
    {
        $this->assertTrue(true);
        $this->assertNotNull($this->api);
    }
}
