<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\EventSubscriber;

use Dbp\Relay\CoreBundle\Helpers\Tools;
use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\PersonPhotoProvider;
use Dbp\Relay\ProxyBundle\EventSubscriber\ProxyDataEventSubscriber as BaseProxyDataEventSubscriber;
use Exception;

class ProxyDataEventSubscriber extends BaseProxyDataEventSubscriber
{
    protected const NAMESPACE = 'greenlight';

    private const GET_PHOTO_DATA_FOR_USER_FUNCTION_NAME = 'getPhotoDataForUser';
    private const USER_ID_PARAMETER_NAME = 'userId';

    /** @var PersonPhotoProvider */
    private $dataProvider;

    public function __construct(PersonPhotoProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    protected function isFunctionDefined(string $functionName): bool
    {
        return $functionName === self::GET_PHOTO_DATA_FOR_USER_FUNCTION_NAME;
    }

    protected function areAllRequiredArgumentsDefined(array $arguments): bool
    {
        return !Tools::isNullOrEmpty($arguments[self::USER_ID_PARAMETER_NAME] ?? null);
    }

    /**
     * @throws Exception
     */
    protected function callFunction(string $functionName, array $arguments)
    {
        $returnValue = null;

        if ($functionName === self::GET_PHOTO_DATA_FOR_USER_FUNCTION_NAME) {
            $userId = $arguments[self::USER_ID_PARAMETER_NAME];
            $imageData = $this->dataProvider->getPhotoDataForUser($userId);
            $returnValue = base64_encode($imageData);
        }

        return $returnValue;
    }
}
