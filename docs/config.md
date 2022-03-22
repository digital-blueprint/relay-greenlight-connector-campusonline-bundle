# Configuration

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
