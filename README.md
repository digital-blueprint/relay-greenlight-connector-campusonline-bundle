# DbpRelayGreenlightConnectorCampusonlineBundle

[GitLab](https://gitlab.tugraz.at/dbp/greenlight/dbp-relay-greenlight-connector-campusonline-bundle) | [Packagist](https://packagist.org/packages/dbp/relay-greenlight-connector-campusonline-bundle)

This bundle fetches images for [DbpRelayGreenlightBundle](https://gitlab.tugraz.at/dbp/greenlight/dbp-relay-greenlight-bundle)
from CampusOnline, while retrieving the `co-obfuscated-c-ident` from LDAP.

## Bundle installation

You can install the bundle directly from [packagist.org](https://packagist.org/packages/dbp/relay-greenlight-bundle).

```bash
composer require dbp/relay-greenlight-connector-campusonline-bundle
```

## Integration into the API Server

* Add the necessary bundles to your `config/bundles.php`:

```php
...
Dbp\Relay\GreenlightConnectorCampusonlineBundle\DbpRelayGreenlightConnectorCampusonlineBundle::class => ['all' => true],
Dbp\Relay\CoreBundle\DbpRelayCoreBundle::class => ['all' => true],
];
```

* Run `composer install` to clear caches

## Configuration

The bundle has a `secret_token` configuration value that you can specify in your
app, either by hardcoding it, or by referencing an environment variable.

For this create `config/packages/dbp_relay_greenlight_connector_campusonline.yaml` in the app with the following
content:

```yaml
dbp_relay_greenlight_connector_campusonline:
  campusonline:
    # The base URL of the CO instance
    api_url:              '%env(GREENLIGHT_CONNECTOR_CAMPUSONLINE_API_URL)%' # Example: 'https://online.mycampus.org/campus_online'

    # The OAuth2 client ID
    client_id:            '%env(GREENLIGHT_CONNECTOR_CAMPUSONLINE_CLIENT_ID)%' # Example: my-client

    # The OAuth2 client secret
    client_secret:        '%env(GREENLIGHT_CONNECTOR_CAMPUSONLINE_CLIENT_SECRET)%' # Example: my-secret

    # The dataservice name of the ucardfoto service in case the default isn't used
    dataservice:          '%env(GREENLIGHT_CONNECTOR_CAMPUSONLINE_DATASERVICE)%'
  ldap:
    host:                 '%env(GREENLIGHT_CONNECTOR_CAMPUSONLINE_LDAP_HOST)%'
    base_dn:              '%env(GREENLIGHT_CONNECTOR_CAMPUSONLINE_LDAP_BASE_DN)%'
    username:             '%env(GREENLIGHT_CONNECTOR_CAMPUSONLINE_LDAP_USERNAME)%'
    password:             '%env(GREENLIGHT_CONNECTOR_CAMPUSONLINE_LDAP_PASSWORD)%'
    identifier_attribute: '%env(GREENLIGHT_CONNECTOR_CAMPUSONLINE_LDAP_IDENTIFIER_ATTRIBUTE)%'
    co_ident_nr_obfuscated_attribute: '%env(GREENLIGHT_CONNECTOR_CAMPUSONLINE_LDAP_CO_IDENT_NR_OBFUSCATED_ATTRIBUTE)%'
```

If you were using the [DBP API Server Template](https://gitlab.tugraz.at/dbp/relay/dbp-relay-server-template)
as template for your Symfony application, then the configuration file should have already been generated for you.

For more info on bundle configuration see [Symfony bundles configuration](https://symfony.com/doc/current/bundles/configuration.html).

## Development & Testing

* Install dependencies: `composer install`
* Run tests: `composer test`
* Run linters: `composer run lint`
* Run cs-fixer: `composer run cs-fix`

## Bundle dependencies

Don't forget you need to pull down your dependencies in your main application if you are installing packages in a bundle.

```bash
# updates and installs dependencies from dbp/relay-greenlight-connector-campusonline-bundle
composer update dbp/relay-greenlight-connector-campusonline-bundle
```

## Events

This bundle registers the following events before and after a photo for a user is fetched from CampusOnline:

### PersonPhotoProviderPreEvent

This event is fired before a photo is fetched from CampusOnline. With this event you can modify the user id that is used to fetch the photo.
An event listener receives a `Dbp\Relay\GreenlightConnectorCampusonlineBundle\Event\PersonPhotoProviderPreEvent` instance.

To get access to such an event you have to implement **either** an event subscriber (preferred) or a listener.

#### Event Subscriber

The subscriber gets called with a `PersonPhotoProviderPreEvent` object in a service
for example in `src/EventSubscriber/PersonPhotoProviderSubscriber.php`:

```php
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
use the [dbp-relay-server-template](https://gitlab.tugraz.at/dbp/relay/dbp-relay-server-template)
as Symfony application template.

#### Event Listener

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

### PersonPhotoProviderPostEvent

This event is fired after a photo is fetched from CampusOnline. With this event you can modify the photo content that was fetched from CampusOnline.
An event listener receives a `Dbp\Relay\GreenlightConnectorCampusonlineBundle\Event\PersonPhotoProviderPostEvent` instance.

To get access to such an event you have to implement **either** an event subscriber (preferred) or a listener.

#### Event Subscriber

The subscriber gets called with a `PersonPhotoProviderPostEvent` object in a service
for example in `src/EventSubscriber/PersonPhotoProviderSubscriber.php`:

```php
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
use the [dbp-relay-server-template](https://gitlab.tugraz.at/dbp/relay/dbp-relay-server-template)
as Symfony application template.

#### Event Listener

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
