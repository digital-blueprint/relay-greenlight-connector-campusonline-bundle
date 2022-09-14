<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\API;

if (interface_exists('Dbp\Relay\GreenlightBundle\API\PersonPhotoProviderInterface')) {
    /** @psalm-suppress UnrecognizedStatement */
    interface PersonPhotoProviderInterface extends \Dbp\Relay\GreenlightBundle\API\PersonPhotoProviderInterface
    {
    }
} else {
    /** @psalm-suppress UnrecognizedStatement */
    interface PersonPhotoProviderInterface
    {
    }
}
