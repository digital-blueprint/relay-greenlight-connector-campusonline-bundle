<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApiTest extends ApiTestCase
{
    public function testBasics()
    {
        $client = self::createClient();
        $response = $client->request('GET', '/greenlight-connector-campusonline/tests');
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $response = $client->request('GET', '/greenlight-connector-campusonline/tests/graz');
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());

        $response = $client->request('DELETE', '/greenlight-connector-campusonline/tests/graz');
        $this->assertSame(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        $response = $client->request('PUT', '/greenlight-connector-campusonline/tests/graz', [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode(['name' => 'foo']),
        ]);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertSame('foo', json_decode($response->getContent(), true)['name']);
    }

    public function testNoAuth()
    {
        $client = self::createClient();
        $response = $client->request('GET', '/greenlight-connector-campusonline/tests/graz/loggedin-only');
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
    }
}
