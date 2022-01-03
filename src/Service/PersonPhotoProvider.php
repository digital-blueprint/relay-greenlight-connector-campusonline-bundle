<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service;

use Dbp\CampusonlineApi\Rest\ApiException;
use Dbp\CampusonlineApi\Rest\UCard\UCard;
use Dbp\CampusonlineApi\Rest\UCard\UCardType;
use Dbp\Relay\CoreBundle\API\UserSessionInterface;
use Dbp\Relay\GreenlightBundle\API\PersonPhotoProviderInterface;
use Dbp\Relay\GreenlightBundle\Exception\PhotoServiceException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

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
     * @var UserSessionInterface
     */
    private $userSession;

    public function __construct(CampusonlineService $campusonlineService, LdapService $ldapService, UserSessionInterface $userSession)
    {
        $this->campusonlineService = $campusonlineService;
        $this->ldapService = $ldapService;
        $this->userSession = $userSession;
    }

    /**
     * Returns the best usable card or null.
     *
     * @param UCard[] $cards
     */
    public static function selectCard(array $cards): ?UCard
    {
        $cardList = [];

        foreach ($cards as $card) {
            // We want to be reasonably sure that the photo matches the user. Before a card is issued the user
            // can still change the photo, so we only use cards that have already been issued and where changing
            // the photo requires a special request and can't be done online.
            if ($card->isUpdatable) {
                continue;
            }
            // No photo, skip
            if ($card->contentSize === 0) {
                continue;
            }
            // If a user has multiple cards use the "most official" one first
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
            return null;
        }

        // sort by array key
        ksort($cardList);

        // get first item
        return reset($cardList);
    }

    public function getPhotoDataForUser(string $userId): string
    {
        $ident = $this->ldapService->getCoIdent($userId);

        try {
            $cards = $this->campusonlineService->getCardsForCoIdent($ident);
        } catch (ApiException $e) {
            $this->logger->error('Cards could not be fetched: '.$e->getMessage());

            throw new PhotoServiceException($e->getMessage());
        }

        if (count($cards) === 0) {
            throw new PhotoServiceException('No cards found');
        }

        $card = self::selectCard($cards);
        if ($card === null) {
            throw new PhotoServiceException('No suitable card found');
        }

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
        $userId = $this->userSession->getUserIdentifier();
        if ($userId === null) {
            throw new PhotoServiceException('No user ID available');
        }

        return $this->getPhotoDataForUser($userId);
    }
}
