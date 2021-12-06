<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service;

use Dbp\CampusonlineApi\UCard\UCard;
use Dbp\CampusonlineApi\UCard\UCardAPI;
use Dbp\CampusonlineApi\UCard\UCardException;
use Dbp\CampusonlineApi\UCard\UCardPicture;
use Dbp\CampusonlineApi\UCard\UCardType;
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

        $clientId = $config['co_oauth2_ucardapi_client_id'] ?? '';
        $clientSecret = $config['co_oauth2_ucardapi_client_secret'] ?? '';
        $baseUrl = $config['co_oauth2_ucardapi_api_url'] ?? '';
        $dataService = $config['co_oauth2_ucardapi_dataservice'] ?? '';

        $this->service->setBaseUrl($baseUrl);
        $this->service->setDataService($dataService);

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

    /**
     * @throws UCardException
     */
    public function setCardPicture(UCard $card, string $data): void
    {
        $this->fetchServiceTokenIfNeeded();

        $this->service->setCardPicture($card, $data);
    }

    /**
     * @throws UCardException
     */
    public function storeDocument(string $ident, \DateTimeInterface $requestCreatedDate, string $documentType, string $documentData): void
    {
        $this->fetchServiceTokenIfNeeded();

        // FIXME: check if we can handle $documentType

        // FIXME: Force BA for testing purposes
        $cardType = UCardType::BA;

        $cards = $this->service->getCardsForIdent($ident, $cardType);

        // If there exists no card of the specified type we have to create one
        if (count($cards) === 0) {
            $this->service->createCardForIdent($ident, $cardType);
            $cards = $this->service->getCardsForIdent($ident, $cardType);
        }
        assert(count($cards) !== 0 && $cards[0]->cardType === $cardType);
        $card = $cards[0];
        $this->service->setCardPicture($card, $documentData);
    }

    /**
     * @throws UCardException
     */
    public function canStoreDocument(string $ident, string $documentType): bool
    {
        $this->fetchServiceTokenIfNeeded();

        // FIXME: check if we can handle $documentType

        // FIXME: Force BA for testing purposes
        $cardType = UCardType::BA;

        $cards = $this->service->getCardsForIdent($ident, $cardType);
        // XXX: If there exists no card of the specified type we have to create one, otherwise we don't know
        // if we can create/update it
        if (count($cards) === 0) {
            $this->service->createCardForIdent($ident, $cardType);
            $cards = $this->service->getCardsForIdent($ident, $cardType);
        }
        assert(count($cards) !== 0 && $cards[0]->cardType === $cardType);
        $card = $cards[0];

        return $card->isUpdatable;
    }

    /**
     * @throws UCardException
     */
    public function createCardForIdent(string $ident, string $cardType): void
    {
        $this->fetchServiceTokenIfNeeded();

        $this->service->createCardForIdent($ident, $cardType);
    }
}
