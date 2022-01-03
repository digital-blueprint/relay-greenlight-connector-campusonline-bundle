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

    private function getApi(): Api
    {
        if ($this->service === null) {
            $config = $this->config;
            $clientId = $config['client_id'] ?? '';
            $clientSecret = $config['client_secret'] ?? '';
            $baseUrl = $config['api_url'] ?? '';
            $api = new Api($baseUrl, $clientId, $clientSecret);
            foreach ($this->config['dataservice_override'] as $entry) {
                $api->addDataServiceOverride($entry['name'], $entry['replacement']);
            }
            $this->service = $api;
            if ($this->logger !== null) {
                $api->setLogger($this->logger);
            }
        }

        return $this->service;
    }

    public function checkConnection()
    {
        $ucard = $this->getApi()->UCard();
        $ucard->getCardsForIdentIdObfuscated('thisisnotarealidentjustfortesting');
    }

    /**
     * Returns the cards (photos) of an ident.
     *
     * @return UCard[]
     *
     * @throws ApiException
     */
    public function getCardsForIdentIdObfuscated(string $ident, ?string $cardType = null): array
    {
        $ucard = $this->getApi()->UCard();

        return $ucard->getCardsForIdentIdObfuscated($ident, $cardType);
    }

    public function getIdentIdObfuscatedForCoIdent(CoIdent $ident): ?string
    {
        // If we got the identIdObfuscated from LDAP just return it
        $identIdObfuscated = $ident->identIdObfuscated;
        if ($identIdObfuscated !== null) {
            return $identIdObfuscated;
        }

        // Otherwise, call the student API
        $student = $this->getApi()->Student();
        if ($ident->identId !== null) {
            $data = $student->getStudentDataByIdentId($ident->identId);
            if (count($data) > 0) {
                return $data[0]->identIdObfuscated;
            }
        }
        if ($ident->personId !== null) {
            $data = $student->getStudentDataByPersonId($ident->personId);
            if (count($data) > 0) {
                return $data[0]->identIdObfuscated;
            }
        }

        return null;
    }

    /**
     * @return UCard[]
     */
    public function getCardsForCoIdent(CoIdent $ident, ?string $cardType = null): array
    {
        $identIdObfuscated = $this->getIdentIdObfuscatedForCoIdent($ident);
        if ($identIdObfuscated === null) {
            return [];
        }

        $ucard = $this->getApi()->UCard();

        return $ucard->getCardsForIdentIdObfuscated($identIdObfuscated, $cardType);
    }

    /**
     * @throws ApiException
     */
    public function getCardPicture(UCard $card): UCardPicture
    {
        $ucard = $this->getApi()->UCard();

        return $ucard->getCardPicture($card);
    }
}
