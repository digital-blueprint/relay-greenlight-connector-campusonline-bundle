<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class PersonPhotoProviderPreEvent extends Event
{
    public const NAME = 'dbp.relay.greenlight_connector_campusonline.person_photo_provider.pre';

    protected $userId;

    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }
}
