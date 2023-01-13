# Overview

Source: https://github.com/digital-blueprint/dbp-relay-greenlight-connector-campusonline-bundle

```mermaid
graph TD
    style greenlight_connector_bundle fill:#606096,color:#fff

    campus_online("CAMPUSonline")
    ldap("LDAP")

    subgraph API Gateway
        api(("API"))
        greenlight_bundle("Greenlight Bundle")
        greenlight_connector_bundle("Greenlight Connector Bundle")
    end

    api --> greenlight_bundle
    greenlight_bundle --> greenlight_connector_bundle
    greenlight_connector_bundle --> campus_online
    greenlight_connector_bundle --> ldap
```

This bundle connects the greenlight bundle with CAMPUSonline and allows fetching
student photos from issued student cards. This makes it possible to authenticate
students visually via the frontend.

## Installation Requirements

* Access to a CAMPUSonline instance
* A configured CAMPUSonline OAuth client for the `brm.pm.extension.ucardfoto` data service
* An LDAP server for mapping user IDs to CAMPUSonline IDs.
