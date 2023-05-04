# Events

This bundle registers the following events before and after a photo for a user is fetched from CampusOnline:

## PersonPhotoProviderPreEvent

This event is fired before a photo is fetched from CampusOnline. With this event you can modify the user id that is used to fetch the photo.
An event listener receives a `\Dbp\Relay\GreenlightConnectorCampusonlineBundle\Event\PersonPhotoProviderPreEvent` instance.

To get access to such an event you have to implement **either** an event subscriber (preferred) or a listener.

### Event Subscriber

The subscriber gets called with a `PersonPhotoProviderPreEvent` object in a service
for example in `src/EventSubscriber/PersonPhotoProviderSubscriber.php`:

```php
<?php

namespace App\EventSubscriber;

use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Event\PersonPhotoProviderPreEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersonPhotoProviderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            PersonPhotoProviderPreEvent::NAME => 'onPre',
        ];
    }

    public function onPre(PersonPhotoProviderPreEvent $event)
    {
        // Get the user id from the event
        $userId = $event->getUserId();

        // Do something with the user id
        $event->setUserId("something-else");
    }
}
```

If the subscriber service isn't found you may need to configure the service in your `config/services.yml` file or
use the [dbp-relay-server-template](https://github.com/digital-blueprint/relay-server-template)
as Symfony application template.

### Event Listener

You need to configure the listener service in your `config/services.yml` file:

```yaml
  App\EventListener\PersonPhotoProviderPreListener:
    autowire: true
    autoconfigure: true
    tags:
      - { name: kernel.event_listener, event: dbp.relay.greenlight_connector_campusonline.person_photo_provider.pre }
```

The listener gets called with a `PersonPhotoProviderPreEvent` object in your service in `src/EventListener/PersonPhotoProviderPreListener.php`:

```php
<?php

namespace App\EventListener;

use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Event\PersonPhotoProviderPreEvent;

class PersonPhotoProviderPreEvent
{
    public function onDbpRelayGreenlightConnectorCampusonlinePersonPhotoProviderPre(PersonPhotoProviderPreEvent $event)
    {
        // Get the user id from the event
        $userId = $event->getUserId();

        // Do something with the user id
        $event->setUserId("something-else");
    }
}
```

## PersonPhotoProviderPostEvent

This event is fired after a photo is fetched from CampusOnline. With this event you can modify the photo content that was fetched from CampusOnline.
An event listener receives a `\Dbp\Relay\GreenlightConnectorCampusonlineBundle\Event\PersonPhotoProviderPostEvent` instance.

To get access to such an event you have to implement **either** an event subscriber (preferred) or a listener.

### Event Subscriber

The subscriber gets called with a `PersonPhotoProviderPostEvent` object in a service
for example in `src/EventSubscriber/PersonPhotoProviderSubscriber.php`:

```php
<?php

namespace App\EventSubscriber;

use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Event\PersonPhotoProviderPostEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersonPhotoProviderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            PersonPhotoProviderPostEvent::NAME => 'onPost',
        ];
    }

    public function onPost(PersonPhotoProviderPostEvent $event)
    {
        // Get the user id from the event
        $userId = $event->getUserId();

        // Get the photo content from the event
        $photoContent = $event->getPhotoContent();

        // Set a new photo
        $event->setPhotoContent(file_get_contents(__DIR__.'/../Assets/another_photo.jpg'));
    }
}
```

If the subscriber service isn't found you may need to configure the service in your `config/services.yml` file or
use the [dbp-relay-server-template](https://github.com/digital-blueprint/relay-server-template)
as Symfony application template.

### Event Listener

You need to configure the listener service in your `config/services.yml` file:

```yaml
  App\EventListener\PersonPhotoProviderPostListener:
    autowire: true
    autoconfigure: true
    tags:
      - { name: kernel.event_listener, event: dbp.relay.greenlight_connector_campusonline.person_photo_provider.post }
```

The listener gets called with a `PersonPhotoProviderPostEvent` object in your service in `src/EventListener/PersonPhotoProviderPostListener.php`:

```php
<?php

namespace App\EventListener;

use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Event\PersonPhotoProviderPostEvent;

class PersonPhotoProviderPostEvent
{
    public function onDbpRelayGreenlightConnectorCampusonlinePersonPhotoProviderPost(PersonPhotoProviderPostEvent $event)
    {
        // Get the user id from the event
        $userId = $event->getUserId();

        // Get the photo content from the event
        $photoContent = $event->getPhotoContent();

        // Set a new photo
        $event->setPhotoContent(file_get_contents(__DIR__.'/../Assets/another_photo.jpg'));
    }
}
```
