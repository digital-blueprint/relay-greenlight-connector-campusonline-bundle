<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service;

use Dbp\CampusonlineApi\Rest\ApiException;
use Dbp\CampusonlineApi\Rest\UCard\UCardType;
use Dbp\Relay\BasePersonBundle\API\PersonProviderInterface;
use Dbp\Relay\GreenlightBundle\API\PersonPhotoProviderInterface;
use Dbp\Relay\GreenlightBundle\Exception\PhotoServiceException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PersonPhotoProvider implements PersonPhotoProviderInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var CampusonlineService
     */
    private $campusonlineService;

    /**
     * @var LdapService
     */
    private $ldapService;

    /**
     * @var PersonProviderInterface
     */
    private $personProvider;

    public function __construct(CampusonlineService $campusonlineService, LdapService $ldapService, PersonProviderInterface $personProvider)
    {
        $this->campusonlineService = $campusonlineService;
        $this->ldapService = $ldapService;
        $this->personProvider = $personProvider;
    }

    public function getPhotoDataForUser(string $userId): string
    {
        $ident = $this->ldapService->getCoIdentNrObfuscated($userId);
        try {
            $cards = $this->campusonlineService->getCardsForIdent($ident);
        } catch (ApiException $e) {
            $this->logger->error('Cards could not be fetched: '.$e->getMessage());

            throw new PhotoServiceException($e->getMessage());
        }

        $cardList = [];

        foreach ($cards as $card) {
            // We want to be reasonably sure that the photo matches the user. Before a card is issued the user
            // can still change the photo, so we only use cards that have already been issued and where changing
            // the photo requires a special request and can't be done online.
            if ($card->isUpdatable) {
                continue;
            }
            switch ($card->cardType) {
                case UCardType::BA:
                    $cardList['a'] = $card;
                    break;
                case UCardType::BPR:
                    $cardList['b'] = $card;
                    break;
                case UCardType::STA:
                    $cardList['c'] = $card;
                    break;
                case UCardType::EPR:
                    $cardList['d'] = $card;
                    break;
                case UCardType::IR:
                    $cardList['e'] = $card;
                    break;
            }
        }

        if (count($cardList) === 0) {
            return '';
        }

        // sort by array key
        ksort($cardList);

        // get first item
        $card = reset($cardList);

        try {
            $pic = $this->campusonlineService->getCardPicture($card);
        } catch (ApiException $e) {
            $this->logger->error('Card picture could not be fetched: '.$e->getMessage());

            throw new PhotoServiceException($e->getMessage());
        }

        return $pic->content;
    }

    /**
     * Returns the photo of a person as binary data.
     *
     * @throws PhotoServiceException
     */
    public function getPhotoDataForCurrentUser(): string
    {
        try {
            $person = $this->personProvider->getCurrentPerson();
        } catch (NotFoundHttpException $e) {
            $this->logger->error('Current person could not be found: '.$e->getMessage());

            throw new PhotoServiceException($e->getMessage());
        }

        return $this->getPhotoDataForUser($person->getIdentifier());
    }
}
