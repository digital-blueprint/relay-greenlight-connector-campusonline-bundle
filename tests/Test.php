<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Tests;

use Dbp\CampusonlineApi\Rest\UCard\UCard;
use Dbp\CampusonlineApi\Rest\UCard\UCardType;
use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\PersonPhotoProvider;
use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testSelectCards()
    {
        // OK
        $card = PersonPhotoProvider::selectCard([
            new UCard('foo', UCardType::STA, 'bar', 10, false),
        ]);
        $this->assertNotNull($card);

        // Updatable
        $card = PersonPhotoProvider::selectCard([
            new UCard('foo', UCardType::STA, 'bar', 10, true),
        ]);
        $this->assertNull($card);

        // Empty
        $card = PersonPhotoProvider::selectCard([
            new UCard('foo', UCardType::STA, 'bar', 0, false),
        ]);
        $this->assertNull($card);

        // Order
        $card = PersonPhotoProvider::selectCard([
            new UCard('foo', UCardType::STA, 'bar', 42, false),
            new UCard('foo2', UCardType::BA, 'bar', 42, false),
        ]);
        $this->assertSame($card->cardType, UCardType::BA);

        // Order with Updatable
        $card = PersonPhotoProvider::selectCard([
            new UCard('foo', UCardType::STA, 'bar', 42, false),
            new UCard('foo2', UCardType::BA, 'bar', 42, true),
        ]);
        $this->assertSame($card->cardType, UCardType::STA);
    }
}
