<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service;

use Dbp\CampusonlineApi\UCard\UCard;
use Dbp\CampusonlineApi\UCard\UCardAPI;
use Dbp\CampusonlineApi\UCard\UCardException;
use Dbp\CampusonlineApi\UCard\UCardPicture;
use GuzzleHttp\Exception\GuzzleException;

class CampusonlineService
{
    /**
     * @var UCardAPI
     */
    private $service;

    private $serviceHasToken = false;

    private $config;

    public function __construct()
    {
        $this->service = new UCardAPI();
        $this->config = [];
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @throws UCardException
     */
    private function fetchServiceTokenIfNeeded()
    {
        if ($this->serviceHasToken) {
            return;
        }

        $config = $this->config;

        $clientId = $config['client_id'] ?? '';
        $clientSecret = $config['client_secret'] ?? '';
        $baseUrl = $config['api_url'] ?? '';
        $dataService = $config['dataservice'] ?? '';

        $this->service->setBaseUrl($baseUrl);
        if ($dataService !== '') {
            $this->service->setDataService($dataService);
        }

        try {
            $this->service->fetchToken($clientId, $clientSecret);
            $this->serviceHasToken = true;
        } catch (GuzzleException $e) {
        } catch (\JsonException $e) {
        }
    }

    /**
     * Returns the cards (photos) of an ident.
     *
     * @return UCard[]
     *
     * @throws UCardException
     */
    public function getCardsForIdent(string $ident, ?string $cardType = null): array
    {
        $this->fetchServiceTokenIfNeeded();

        return $this->service->getCardsForIdent($ident, $cardType);
    }

    /**
     * @throws UCardException
     */
    public function getCardPicture(UCard $card): UCardPicture
    {
        $this->fetchServiceTokenIfNeeded();

        return $this->service->getCardPicture($card);
    }
}
