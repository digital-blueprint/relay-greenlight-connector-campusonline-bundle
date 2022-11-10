<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\EventSubscriber;

use Dbp\Relay\CoreBundle\ProxyApi\AbstractProxyDataEventSubscriber;
use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\PersonPhotoProvider;
use Exception;

class ProxyDataEventSubscriber extends AbstractProxyDataEventSubscriber
{
    private const NAMESPACE = 'greenlight';

    private const GET_PHOTO_DATA_FOR_USER_FUNCTION_NAME = 'getPhotoDataForUser';
    private const USER_ID_PARAMETER_NAME = 'userId';

    /** @var PersonPhotoProvider */
    private $personPhotoProvider;

    public function __construct(PersonPhotoProvider $personPhotoProvider)
    {
        $this->personPhotoProvider = $personPhotoProvider;
    }

    protected static function getSubscribedNamespace(): string
    {
        return self::NAMESPACE;
    }

    protected static function getAvailableFunctionSignatures(): array
    {
        return [self::GET_PHOTO_DATA_FOR_USER_FUNCTION_NAME => [self::USER_ID_PARAMETER_NAME]];
    }

    /**
     * @throws Exception
     */
    protected function callFunction(string $functionName, array $arguments): ?string
    {
        $returnValue = null;

        if ($functionName === self::GET_PHOTO_DATA_FOR_USER_FUNCTION_NAME) {
            $userId = $arguments[self::USER_ID_PARAMETER_NAME];
            $imageData = $this->personPhotoProvider->getPhotoDataForUser($userId);
            $returnValue = base64_encode($imageData);
        }

        return $returnValue;
    }
}
