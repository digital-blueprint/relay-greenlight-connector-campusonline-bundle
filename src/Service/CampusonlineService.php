<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service;

use Dbp\CampusonlineApi\Rest\Api;
use Dbp\CampusonlineApi\Rest\ApiException;
use Dbp\CampusonlineApi\Rest\UCard\UCard;
use Dbp\CampusonlineApi\Rest\UCard\UCardPicture;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class CampusonlineService implements LoggerAwareInterface
{
    /**
     * @var Api
     */
    private $service;

    private $logger;

    private $config;

    public function __construct()
    {
        $this->config = [];
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        if ($this->service !== null) {
            $this->service->setLogger($logger);
        }
    }

    /**
     * @throws ApiException
     */
    private function getUCardApi()
    {
        if ($this->service === null) {
            $config = $this->config;
            $clientId = $config['client_id'] ?? '';
            $clientSecret = $config['client_secret'] ?? '';
            $baseUrl = $config['api_url'] ?? '';
            $dataService = $config['dataservice'] ?? '';

            $api = new Api($baseUrl, $clientId, $clientSecret);
            $api->addDataServiceOverride('brm.pm.extension.ucardfoto', $dataService);
            $this->service = $api;
            if ($this->logger !== null) {
                $api->setLogger($this->logger);
            }
        }

        return $this->service->UCard();
    }

    public function checkConnection()
    {
        $ucard = $this->getUCardApi();
        $ucard->getCardsForIdentIdObfuscated('thisisnotarealidentjustfortesting');
    }

    /**
     * Returns the cards (photos) of an ident.
     *
     * @return UCard[]
     *
     * @throws ApiException
     */
    public function getCardsForIdent(string $ident, ?string $cardType = null): array
    {
        $ucard = $this->getUCardApi();

        return $ucard->getCardsForIdentIdObfuscated($ident, $cardType);
    }

    /**
     * @throws ApiException
     */
    public function getCardPicture(UCard $card): UCardPicture
    {
        $ucard = $this->getUCardApi();

        return $ucard->getCardPicture($card);
    }
}
