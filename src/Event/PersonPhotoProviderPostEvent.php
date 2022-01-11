<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class PersonPhotoProviderPostEvent extends Event
{
    public const NAME = 'dbp.relay.greenlight_connector_campusonline.person_photo_provider.post';

    protected $userId;
    protected $photoContent;

    public function __construct(string $userId, string $photoContent)
    {
        $this->userId = $userId;
        $this->photoContent = $photoContent;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getPhotoContent(): string
    {
        return $this->photoContent;
    }
}
