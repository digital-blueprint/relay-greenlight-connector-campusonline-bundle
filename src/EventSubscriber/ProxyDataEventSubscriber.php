<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\EventSubscriber;

use Dbp\Relay\CoreBundle\Helpers\Tools;
use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\PersonPhotoProvider;
use Dbp\Relay\ProxyBundle\Event\ProxyDataEvent;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProxyDataEventSubscriber implements EventSubscriberInterface
{
    private const NAMESPACE = 'greenlight';
    private const GET_PHOTO_DATA_FOR_USER_FUNCTION_NAME = 'getPhotoDataForUser';
    private const USER_ID_PARAMETER_NAME = 'userId';

    private $dataProvider;

    public function __construct(PersonPhotoProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProxyDataEvent::NAME.self::NAMESPACE => 'onProxyDataRequest',
         ];
    }

    public function onProxyDataRequest(ProxyDataEvent $event)
    {
        $proxyData = $event->getProxyData();
        $arguments = $proxyData->getArguments();

        $returnValue = null;

        switch ($proxyData->getFunctionName()) {
            case self::GET_PHOTO_DATA_FOR_USER_FUNCTION_NAME:
                try {
                    $userId = $arguments[self::USER_ID_PARAMETER_NAME] ?? null;
                    if (Tools::isNullOrEmpty($userId)) {
                        $proxyData->setErrorsFromException(new Exception(
                            sprintf('missing parameter "%s" for function "%s" under namespace "%s"',
                                self::USER_ID_PARAMETER_NAME, $proxyData->getFunctionName(), $proxyData->getNamespace()), 400));
                    } else {
                        $imageData = $this->dataProvider->getPhotoDataForUser($userId);
                        $returnValue = base64_encode($imageData);
                    }
                } catch (\Exception $exception) {
                    $proxyData->setErrorsFromException($exception);
                }
                break;
            default:
                $proxyData->setErrorsFromException(new Exception(
                    sprintf('unknown function "%s" under namespace "%s"', $proxyData->getFunctionName(), $proxyData->getNamespace()), 400));
                break;
        }

        $proxyData->setData($returnValue);
    }
}
