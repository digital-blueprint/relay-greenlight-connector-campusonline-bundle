# Configuration

## Recipe

The default [Symfony recipe](https://github.com/digital-blueprint/symfony-recipes/tree/main/dbp/relay-greenlight-connector-campusonline-bundle)
creates a minimal configuration with the following environment variables:

```bash
GREENLIGHT_CONNECTOR_CAMPUSONLINE_API_URL=https://campusonline.your.domain
GREENLIGHT_CONNECTOR_CAMPUSONLINE_CLIENT_ID=client-id
GREENLIGHT_CONNECTOR_CAMPUSONLINE_CLIENT_SECRET=secret
GREENLIGHT_CONNECTOR_CAMPUSONLINE_LDAP_HOST=directory.your.domain
GREENLIGHT_CONNECTOR_CAMPUSONLINE_LDAP_BASE_DN=ou=users,o=uni
GREENLIGHT_CONNECTOR_CAMPUSONLINE_LDAP_USERNAME=cn=your_api,o=uni
GREENLIGHT_CONNECTOR_CAMPUSONLINE_LDAP_PASSWORD=secret
GREENLIGHT_CONNECTOR_CAMPUSONLINE_LDAP_IDENTIFIER_ATTRIBUTE=cn
GREENLIGHT_CONNECTOR_CAMPUSONLINE_LDAP_CO_IDENT_NR_OBFUSCATED_ATTRIBUTE=co-obfuscated-c-ident
```

!!! tip

    Consider putting the `GREENLIGHT_CONNECTOR_CAMPUSONLINE_CLIENT_SECRET`
    and `GREENLIGHT_CONNECTOR_CAMPUSONLINE_LDAP_PASSWORD` in your `.env.local` file, because of the secret information.

## Bundle Configuration

Created via `./bin/console config:dump-reference DbpRelayGreenlightConnectorCampusonlineBundle | sed '/^$/d'`

```yaml
# Default configuration for "DbpRelayGreenlightConnectorCampusonlineBundle"
dbp_relay_greenlight_connector_campusonline:
    campusonline:
        # The base URL of the CO instance
        api_url:              ~ # Required, Example: 'https://online.mycampus.org/campus_online'
        # The OAuth2 client ID
        client_id:            ~ # Required, Example: my-client
        # The OAuth2 client secret
        client_secret:        ~ # Required, Example: my-secret
        # An optional mapping of dataservice IDs to their replacements
        dataservice_override:
            # Prototype
            -
                # The name of the dataservice to override
                name:                 ~ # Required, Example: brm.pm.extension.ucardfoto
                # The replacement dataservice
                replacement:          ~ # Required, Example: loc_locinucfotods.ucardfoto
    ldap:
        host:                 ~ # Required
        base_dn:              ~ # Required
        username:             ~ # Required
        password:             ~ # Required
        # simple_tls uses port 636 and is sometimes referred to as "SSL", start_tls uses port 389 and is sometimes referred to as "TLS"
        encryption:           start_tls # One of "start_tls"; "simple_tls"
        identifier_attribute: ~ # Required
        # LDAP attribute names that correspond to IDs in CAMPUSonline. At least one of the attributes needs to be set
        co_identifier_attributes:
            # The LDAP attribute name for IDENT_NR_OBFUSCATED
            ident_nr_obfuscated:  ~
            # The LDAP attribute name for IDENT_NR
            ident_nr:             ~
            # The LDAP attribute name for PERSON_NR
            person_nr:            ~
```

## Customization

This bundle registers an event before and after a photo for a user is fetched from CampusOnline to be able to modify
the user id that is used to fetch a photo and to modify the photo that is fetched.

Please see [DbpRelayGreenlightConnectorCampusonlineBundle Events](https://gitlab.tugraz.at/dbp/greenlight/dbp-relay-greenlight-connector-campusonline-bundle#events)
for more information.
